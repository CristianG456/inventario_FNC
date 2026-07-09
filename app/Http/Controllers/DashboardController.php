<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\TipoRecurso;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalEquipos     = Equipo::count();
        $activos          = Equipo::where('estado_operativo', 'activo')->count();
        $enMantenimiento  = Equipo::where('estado_operativo', 'mantenimiento')->count();
        $deBaja           = Equipo::where('estado_operativo', 'baja')->count();

        $equiposPorTipo   = TipoRecurso::withCount('equipos')
            ->orderByDesc('equipos_count')
            ->get();

        $ultimosEquipos   = Equipo::select(['id', 'tipo_recurso_id', 'nombre_equipo', 'serial', 'marca', 'modelo', 'estado_operativo', 'created_at'])
            ->with([
                'tipoRecurso:id,nombre',
                'usuarioAsignado:id,equipo_id,nombre,cedula'
            ])
            ->latest()
            ->limit(5)
            ->get();

        $hoy = now();
        $alertasRojas = \App\Models\Licencia::where('estado', 'Vencida')
            ->orWhere('fecha_vencimiento', '<', $hoy->toDateString())
            ->count();
            
        $alertasAmarillas = \App\Models\Licencia::where('fecha_vencimiento', '>=', $hoy->toDateString())
            ->where('fecha_vencimiento', '<=', $hoy->copy()->addDays(30)->toDateString())
            ->where('estado', 'Activa')
            ->count();

        return view('dashboard', compact(
            'totalEquipos',
            'activos',
            'enMantenimiento',
            'deBaja',
            'equiposPorTipo',
            'ultimosEquipos',
            'alertasRojas',
            'alertasAmarillas'
        ));
    }
}
