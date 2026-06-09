<?php

namespace App\Http\Controllers;

use App\Http\Requests\HistorialTecnicoRequest;
use App\Models\Equipo;
use App\Models\HistorialTecnico;
use App\Services\HistorialTecnicoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistorialTecnicoController extends Controller
{
    public function __construct(
        private readonly HistorialTecnicoService $historialTecnicoService
    ) {}
    /**
     * Listado global con filtros y buscador.
     */
    public function index(Request $request): View
    {
        $query = HistorialTecnico::with(['equipo', 'registradoPor'])
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('descripcion', 'like', "%{$request->buscar}%")
                        ->orWhere('usuario_responsable', 'like', "%{$request->buscar}%")
                        ->orWhereHas('equipo', fn($e) =>
                            $e->where('nombre_equipo', 'like', "%{$request->buscar}%")
                              ->orWhere('serial', 'like', "%{$request->buscar}%")
                        );
                });
            })
            ->when($request->filled('tipo_evento'), fn($q) =>
                $q->where('tipo_evento', $request->tipo_evento)
            )
            ->when($request->filled('fecha_desde'), fn($q) =>
                $q->whereDate('fecha_evento', '>=', $request->fecha_desde)
            )
            ->when($request->filled('fecha_hasta'), fn($q) =>
                $q->whereDate('fecha_evento', '<=', $request->fecha_hasta)
            )
            ->orderByDesc('fecha_evento');

        $registros    = $query->paginate(15)->withQueryString();
        $tiposEvento  = HistorialTecnico::TIPOS_EVENTO;

        $conteoCreados = HistorialTecnico::where('estado', 'Creado')->count();
        $conteoProceso = HistorialTecnico::where('estado', 'En proceso')->count();
        $conteosSuspendidos = HistorialTecnico::where('estado', 'Suspendido')->count();
        $conteoFinalizados = HistorialTecnico::where('estado', 'Finalizado')->count();

        return view('historial_tecnico.index', compact(
            'registros', 
            'tiposEvento', 
            'conteoCreados', 
            'conteoProceso', 
            'conteosSuspendidos', 
            'conteoFinalizados'
        ));
    }

    /**
     * Timeline de historial técnico de un equipo específico.
     */
    public function porEquipo(Equipo $equipo): View
    {
        $registros   = $equipo->historialTecnico()->with('registradoPor')->get();
        $tiposEvento = HistorialTecnico::TIPOS_EVENTO;

        return view('historial_tecnico.por_equipo', compact('equipo', 'registros', 'tiposEvento'));
    }

    /**
     * Formulario de creación.
     */
    public function create(Request $request): View
    {
        $tiposEvento = HistorialTecnico::TIPOS_EVENTO;
        $equipoId    = $request->equipo_id;
        $equipo      = $equipoId ? Equipo::find($equipoId) : null;

        return view('historial_tecnico.create', compact('tiposEvento', 'equipo'));
    }

    /**
     * Guarda nuevo evento técnico con snapshot del usuario actual.
     */
    public function store(HistorialTecnicoRequest $request): RedirectResponse
    {
        $equipo = Equipo::findOrFail($request->equipo_id);

        $this->historialTecnicoService->registrarEvento(
            $equipo,
            $request->validated(),
            $request->file('archivos'),
            auth()->id()
        );

        return redirect()
            ->route('historial-tecnico.por-equipo', $equipo)
            ->with('success', 'Evento técnico registrado correctamente.');
    }

    /**
     * Detalle de un evento técnico.
     */
    public function show(HistorialTecnico $historialTecnico): View
    {
        $historialTecnico->load(['equipo', 'registradoPor']);
        return view('historial_tecnico.show', compact('historialTecnico'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(HistorialTecnico $historialTecnico): View
    {
        $tiposEvento = HistorialTecnico::TIPOS_EVENTO;
        $historialTecnico->load('equipo');
        return view('historial_tecnico.edit', compact('historialTecnico', 'tiposEvento'));
    }

    /**
     * Actualizar evento técnico.
     */
    public function update(HistorialTecnicoRequest $request, HistorialTecnico $historialTecnico): RedirectResponse
    {
        $historialTecnico->update($request->validated());

        return redirect()
            ->route('historial-tecnico.show', $historialTecnico)
            ->with('success', 'Evento técnico actualizado correctamente.');
    }

    /**
     * Eliminar (soft delete).
     */
    public function destroy(HistorialTecnico $historialTecnico): RedirectResponse
    {
        $equipoId = $historialTecnico->equipo_id;
        $historialTecnico->delete();

        return redirect()
            ->route('historial-tecnico.por-equipo', $equipoId)
            ->with('success', 'Evento técnico eliminado.');
    }
}
