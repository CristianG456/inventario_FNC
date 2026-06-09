<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsignacionRequest;
use App\Models\Asignacion;
use App\Models\Equipo;
use App\Services\AsignacionService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * Procesa acciones sobre un equipo (asignar, reemplazar, retirar, baja, mantenimiento).
     */
    public function store(AsignacionRequest $request): RedirectResponse
    {
        $equipo  = Equipo::findOrFail($request->equipo_id);
        $usuario = auth()->user();
        $accion  = $request->tipo_accion;

        $asignacion = match ($accion) {
            'asignacion'    => $this->asignacionService->asignar(
                $equipo, $request->validated(), $usuario
            ),
            'reemplazo'     => $this->asignacionService->reemplazar(
                $equipo, $request->validated(), $usuario
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
                $equipo, $request->motivo, $usuario
            ),
            default         => abort(422, 'Acción no válida'),
        };

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
