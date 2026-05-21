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

        $ultimosEquipos   = Equipo::with(['tipoRecurso', 'usuarioAsignado'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalEquipos',
            'activos',
            'enMantenimiento',
            'deBaja',
            'equiposPorTipo',
            'ultimosEquipos'
        ));
    }
}
