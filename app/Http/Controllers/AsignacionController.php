<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsignacionRequest;
use App\Models\Asignacion;
use App\Models\AutorizacionActivo;
use App\Models\Equipo;
use App\Models\Funcionario;
use App\Services\AsignacionService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AsignacionController extends Controller
{
    public function __construct(
        private readonly AsignacionService $asignacionService,
        private readonly PdfService        $pdfService
    ) {}

    /**
     * Listado global de asignaciones con filtros.
     */
    public function index(Request $request): View
    {
        $query = Asignacion::with(['equipo', 'registradoPor'])
            ->when($request->filled('equipo'), fn($q) =>
                $q->where('equipo_id', $request->equipo)
            )
            ->when($request->filled('tipo_accion'), fn($q) =>
                $q->where('tipo_accion', $request->tipo_accion)
            )
            ->when($request->filled('buscar'), fn($q) =>
                $q->where(function($sub) use ($request) {
                    $sub->where('usuario_nombre', 'like', "%{$request->buscar}%")
                        ->orWhere('usuario_cedula', 'like', "%{$request->buscar}%")
                        ->orWhereHas('equipo', fn($e) =>
                            $e->where('nombre_equipo', 'like', "%{$request->buscar}%")
                              ->orWhere('serial', 'like', "%{$request->buscar}%")
                        );
                })
            )
            ->orderByDesc('fecha_accion');

        $asignaciones = $query->paginate(15)->withQueryString();
        $tiposAccion  = Asignacion::TIPOS_ACCION;

        return view('asignaciones.index', compact('asignaciones', 'tiposAccion'));
    }

    /**
     * Historial de asignaciones de un equipo específico.
     */
    public function porEquipo(Equipo $equipo): View
    {
        $asignaciones = $equipo->asignaciones()
            ->with('registradoPor')
            ->paginate(20);

        return view('asignaciones.por_equipo', compact('equipo', 'asignaciones'));
    }

    /**
     * Lista funcionarios elegibles para asignación rápida.
     * Elegible = sin activos o con al menos una autorización disponible para activo adicional.
     */
    public function funcionariosElegibles(Request $request): JsonResponse
    {
        $termino = trim((string) $request->query('q', ''));

        $funcionarios = Funcionario::query()
            ->where('estado', 'Activo')
            ->when($termino !== '', function ($q) use ($termino) {
                $q->where(function ($sub) use ($termino) {
                    $sub->where('identificacion', 'like', "%{$termino}%")
                        ->orWhere('nombres', 'like', "%{$termino}%")
                        ->orWhere('apellidos', 'like', "%{$termino}%")
                        ->orWhere('cargo', 'like', "%{$termino}%")
                        ->orWhere('area', 'like', "%{$termino}%");
                });
            })
            ->withCount([
                'equiposAsignados as activos_count',
                'autorizacionesActivos as autorizaciones_disponibles_count' => fn ($q) =>
                    $q->where('estado', AutorizacionActivo::ESTADO_CARGADA),
            ])
            ->orderBy('nombres')
            ->limit(200)
            ->get();

        $enriquecidos = $funcionarios->map(function ($f) {
            $activos = (int) $f->activos_count;
            $autorizacionesDisponibles = (int) $f->autorizaciones_disponibles_count;
            $esElegible = $activos === 0 || $autorizacionesDisponibles >= 1;

            return [
                'id' => $f->id,
                'identificacion' => $f->identificacion,
                'nombre' => trim("{$f->nombres} {$f->apellidos}"),
                'cargo' => $f->cargo,
                'area' => $f->area,
                'departamento' => $f->departamento,
                'ciudad' => $f->ciudad,
                'empresa_funcionario' => $f->empresa_funcionario,
                'tipo_vinculacion' => $f->tipo_vinculacion,
                'activos_count' => $activos,
                'autorizaciones_count' => $autorizacionesDisponibles,
                'es_elegible' => $esElegible,
                'autorizaciones_faltantes' => $activos > 0 && $autorizacionesDisponibles < 1 ? 1 : 0,
            ];
        });

        $elegibles = $enriquecidos
            ->filter(fn ($f) => $f['es_elegible'])
            ->values();

        $bloqueadosCoincidentes = $enriquecidos
            ->filter(fn ($f) => !$f['es_elegible'])
            ->values()
            ->map(function ($f) {
                return [
                    'id' => $f['id'],
                    'identificacion' => $f['identificacion'],
                    'nombre' => $f['nombre'],
                    'activos_count' => $f['activos_count'],
                    'autorizaciones_count' => $f['autorizaciones_count'],
                    'autorizaciones_faltantes' => $f['autorizaciones_faltantes'],
                ];
            });

        return response()->json([
            'data' => $elegibles,
            'bloqueados' => $bloqueadosCoincidentes,
        ]);
    }

    /**
     * Procesa acciones sobre un equipo (asignar, reemplazar, retirar, baja, mantenimiento).
     */
    public function store(AsignacionRequest $request): RedirectResponse
    {
        $equipo  = Equipo::findOrFail($request->equipo_id);
        $usuario = Auth::user();
        $accion  = $request->tipo_accion;
        $datos   = $request->validated();

        $asignacion = match ($accion) {
            'asignacion'    => $this->asignacionService->asignar(
                $equipo, $datos, $usuario
            ),
            'reemplazo'     => $this->asignacionService->reemplazar(
                $equipo, $datos, $usuario
            ),
            'retiro'        => $this->asignacionService->retirar(
                $equipo, $request->motivo, $usuario, $request->observaciones
            ),
            'mantenimiento' => $this->asignacionService->pasarAMantenimiento(
                $equipo, $request->motivo, $usuario, $request->observaciones
            ),
            'baja'          => $this->asignacionService->darDeBaja(
                $equipo, $request->motivo, $usuario, $request->observaciones
            ),
            'restauracion'  => $this->asignacionService->restaurar(
                $equipo, $request->motivo, $usuario, $request->observaciones
            ),
            default         => abort(422, 'Acción no válida'),
        };

        $returnTo = (string) $request->input('return_to', '');

        if ($returnTo !== '' && Str::startsWith($returnTo, [url('/equipos'), url('/historial-tecnico')])) {
            return redirect($returnTo)->with('success', 'Acción registrada correctamente.');
        }

        return redirect()
            ->route('equipos.show', $equipo)
            ->with('success', 'Acción registrada correctamente.');
    }

    /**
     * Detalle de una asignación.
     */
    public function show(Asignacion $asignacion): View
    {
        $asignacion->load(['equipo.tipoRecurso', 'registradoPor']);
        return view('asignaciones.show', compact('asignacion'));
    }

    /**
     * Genera y descarga el PDF del acta de entrega.
     */
    public function generarPdf(Asignacion $asignacion)
    {
        return $this->pdfService->generarActaEntrega($asignacion);
    }
}
