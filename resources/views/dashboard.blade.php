@extends('layouts.inventario')

@section('title', 'Inicio')

@section('content')

{{-- Alertas de Licencias --}}
@if(isset($alertasRojas) && $alertasRojas > 0 && !session('licencias_vencidas_alert_shown'))
@php session()->put('licencias_vencidas_alert_shown', true); @endphp
<div class="alert alert-danger d-flex align-items-center mb-4 licencia-alerta" role="alert">
    <i class="bi bi-exclamation-octagon-fill fs-4 me-3"></i>
    <div>
        <strong>¡Atención!</strong> Hay {{ $alertasRojas }} licencia(s) vencida(s). <a href="{{ route('licencias.index', ['estado' => 'Vencida']) }}" class="alert-link">Ver licencias</a>.
    </div>
</div>
@endif

@if(isset($alertasAmarillas) && $alertasAmarillas > 0 && !session('licencias_por_vencer_alert_shown'))
@php session()->put('licencias_por_vencer_alert_shown', true); @endphp
<div class="alert alert-warning d-flex align-items-center mb-4 licencia-alerta" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
    <div>
        <strong>Aviso:</strong> Hay {{ $alertasAmarillas }} licencia(s) por vencer en los próximos 30 días. <a href="{{ route('licencias.index') }}" class="alert-link">Revisar licencias</a>.
    </div>
</div>
@endif

{{-- Resumen General del Inventario --}}
<div class="dashboard-summary-panel mb-4">
    <div class="dashboard-summary-header">
        <h2 class="dashboard-summary-title">
            <i class="bi bi-graph-up"></i> RESUMEN GENERAL DEL INVENTARIO
        </h2>
        <p class="dashboard-summary-subtitle">Estado actual del inventario tecnológico</p>
    </div>
    
    <div class="dashboard-summary-divider"></div>
    

    
    <div class="dashboard-summary-states">
        {{-- Total Equipos como una tarjeta normal, destacada --}}
        <div class="dashboard-state-item" style="border: 2px solid #0d6efd; background-color: #f8fbff; cursor: pointer;" onclick="mostrarEstadisticas('total', 'Total Registrados', {{ $totalEquipos }})">
            <div class="dashboard-state-icon text-primary" style="background-color: transparent;">
                <i class="bi bi-laptop fs-4"></i>
            </div>
            <div class="dashboard-state-content">
                <div class="dashboard-state-label text-primary fw-bold text-uppercase" style="font-size: 0.8rem;">Total Registrados</div>
                <div class="dashboard-state-value text-primary fw-bolder fs-4">{{ $totalEquipos }}</div>
            </div>
        </div>
        <div class="dashboard-state-item" style="cursor: pointer;" onclick="mostrarEstadisticas('activo', 'Activos', {{ $activos }})">
            <div class="dashboard-state-icon active">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="dashboard-state-content">
                <div class="dashboard-state-label">Activos</div>
                <div class="dashboard-state-value active">{{ $activos }}</div>
            </div>
        </div>

        <div class="dashboard-state-item" style="cursor: pointer;" onclick="mostrarEstadisticas('disponible', 'Disponibles', {{ $disponibles }})">
            <div class="dashboard-state-icon active">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="dashboard-state-content">
                <div class="dashboard-state-label">Disponibles</div>
                <div class="dashboard-state-value active">{{ $disponibles }}</div>
            </div>
        </div>


        
        <div class="dashboard-state-item" style="cursor: pointer;" onclick="mostrarEstadisticas('mantenimiento', 'En Mantenimiento', {{ $enMantenimiento }})">
            <div class="dashboard-state-icon maintenance">
                <i class="bi bi-tools"></i>
            </div>
            <div class="dashboard-state-content">
                <div class="dashboard-state-label">En mantenimiento</div>
                <div class="dashboard-state-value maintenance">{{ $enMantenimiento }}</div>
            </div>
        </div>
        
        <div class="dashboard-state-item" style="cursor: pointer;" onclick="mostrarEstadisticas('baja', 'Dados de baja', {{ $deBaja }})">
            <div class="dashboard-state-icon inactive">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="dashboard-state-content">
                <div class="dashboard-state-label">Dados de baja</div>
                <div class="dashboard-state-value inactive">{{ $deBaja }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Panel Dinámico de Equipos por Tipo --}}
    <div class="col-12 col-lg-5">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white fw-semibold border-0 pt-4 px-4 fs-5" id="dynamic-stats-title">
                <i class="bi bi-pie-chart me-2 text-primary"></i>Equipos por Tipo
            </div>
            <div class="card-body px-4 pb-4" id="dynamic-stats-container">
                <!-- Javascript inyectará las barras aquí -->
            </div>
        </div>
    </div>

    {{-- Panel Dinámico de Últimos Equipos --}}
    <div class="col-12 col-lg-7">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white fw-semibold border-0 pt-4 px-4 d-flex justify-content-between align-items-center" id="dynamic-latest-title">
                <span class="fs-5"><i class="bi bi-clock-history me-2 text-primary"></i>Últimos Equipos</span>
                <a href="{{ route('equipos.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Equipo</th>
                                <th>Tipo</th>
                                <th>Usuario</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="dynamic-latest-container">
                            <!-- Javascript inyectará las filas aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    // Obtener las estadísticas directamente en la vista para no tocar los controladores (backend)
    $stats = \App\Models\Equipo::select('estado_operativo', 'tipo_recurso_id', \DB::raw('count(*) as count'))
        ->with('tipoRecurso:id,nombre')
        ->groupBy('estado_operativo', 'tipo_recurso_id')
        ->get();
    
    // Obtener últimos equipos por estado dinámicamente
    $estadosArray = ['activo', 'disponible', 'mantenimiento', 'baja'];
    
    $datosPorEstado = [
        'total' => $equiposPorTipo->map(fn($t) => ['nombre' => $t->nombre, 'count' => $t->equipos_count])->values()->toArray(),
        'activo' => [], 'disponible' => [], 'mantenimiento' => [], 'baja' => []
    ];
    
    $ultimosPorEstado = [
        'total' => $ultimosEquipos->map(function($e) {
            return [
                'id' => $e->id,
                'nombre_equipo' => $e->nombre_equipo,
                'serial' => $e->serial_visual,
                'tipo' => $e->tipoRecurso ? $e->tipoRecurso->nombre : '—',
                'usuario' => $e->usuarioAsignado ? $e->usuarioAsignado->nombre : '—',
                'estado_badge' => $e->estado_badge,
                'estado_label' => $e->estado_label,
                'url' => route('equipos.show', $e->id)
            ];
        })->toArray(),
    ];
    
    foreach($stats as $stat) {
        if(isset($datosPorEstado[$stat->estado_operativo])) {
            $datosPorEstado[$stat->estado_operativo][] = [
                'nombre' => $stat->tipoRecurso ? $stat->tipoRecurso->nombre : 'Sin Clasificar',
                'count' => $stat->count
            ];
        }
    }
    
    foreach($datosPorEstado as $key => &$arr) {
        usort($arr, fn($a, $b) => $b['count'] <=> $a['count']);
    }
    
    foreach($estadosArray as $est) {
        $ultimos = \App\Models\Equipo::where('estado_operativo', $est)
            ->select(['id', 'tipo_recurso_id', 'nombre_equipo', 'serial', 'activo_fijo', 'placa', 'estado_operativo'])
            ->with(['tipoRecurso:id,nombre', 'usuarioAsignado:id,equipo_id,nombre'])
            ->latest()
            ->limit(5)
            ->get();
            
        $ultimosPorEstado[$est] = $ultimos->map(function($e) {
            return [
                'id' => $e->id,
                'nombre_equipo' => $e->nombre_equipo,
                'serial' => $e->serial_visual,
                'tipo' => $e->tipoRecurso ? $e->tipoRecurso->nombre : '—',
                'usuario' => $e->usuarioAsignado ? $e->usuarioAsignado->nombre : '—',
                'estado_badge' => $e->estado_badge,
                'estado_label' => $e->estado_label,
                'url' => route('equipos.show', $e->id)
            ];
        })->toArray();
    }
@endphp
@endsection

@push('scripts')
<script>
    const estadisticas = @json($datosPorEstado);
    const ultimosEquipos = @json($ultimosPorEstado);

    function mostrarEstadisticas(estado, titulo, total) {
        // --- Barras de Tipos (Izquierda) ---
        const data = estadisticas[estado] || [];
        const container = document.getElementById('dynamic-stats-container');
        const titleEl = document.getElementById('dynamic-stats-title');
        
        titleEl.innerHTML = `<i class="bi bi-pie-chart me-2 text-primary"></i> Equipos por Tipo: <span class="fw-bold">${titulo}</span>`;
        
        // Efecto visual al hacer clic en las tarjetas (verificando evento)
        if(typeof event !== 'undefined' && event && event.currentTarget && event.currentTarget.classList) {
            document.querySelectorAll('.dashboard-state-item').forEach(el => el.classList.remove('shadow'));
            event.currentTarget.classList.add('shadow');
        }

        if (data.length === 0) {
            container.innerHTML = `<div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                No hay equipos registrados en este estado.
            </div>`;
        } else {
            let html = '';
            data.forEach(item => {
                const porcentaje = total > 0 ? (item.count / total * 100) : 0;
                html += `
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-medium">${item.nombre}</span>
                            <span class="badge bg-primary rounded-pill">${item.count}</span>
                        </div>
                        <div class="progress dashboard-progress-bar" style="height: 8px;">
                            <div class="progress-bar rounded-pill" style="width: ${porcentaje}%"></div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
        
        // --- Tabla Dinámica de Últimos Equipos (Derecha) ---
        const ultimos = ultimosEquipos[estado] || [];
        const latestContainer = document.getElementById('dynamic-latest-container');
        const latestTitle = document.getElementById('dynamic-latest-title').querySelector('span');
        
        latestTitle.innerHTML = `<i class="bi bi-clock-history me-2 text-primary"></i> Últimos Equipos: <span class="fw-bold">${titulo}</span>`;

        if (ultimos.length === 0) {
            latestContainer.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">Sin equipos recientes para este estado.</td></tr>`;
        } else {
            let tableHtml = '';
            ultimos.forEach(eq => {
                tableHtml += `
                    <tr>
                        <td>
                            <a href="${eq.url}" class="text-decoration-none fw-medium">
                                ${eq.nombre_equipo}
                            </a>
                            <br><small class="text-muted">${eq.serial || 'Sin serial'}</small>
                        </td>
                        <td>${eq.tipo}</td>
                        <td>${eq.usuario}</td>
                        <td>
                            <span class="badge bg-${eq.estado_badge}">
                                ${eq.estado_label}
                            </span>
                        </td>
                    </tr>
                `;
            });
            latestContainer.innerHTML = tableHtml;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Inicializar mostrando el total general, seleccionando visualmente el total
        const totalCard = document.querySelector('.dashboard-state-item');
        if(totalCard) totalCard.classList.add('shadow');
        mostrarEstadisticas('total', 'Total Registrados', {{ $totalEquipos }});
    });

    setTimeout(function() {
        const alertas = document.querySelectorAll('.licencia-alerta');
        alertas.forEach(alerta => {
            alerta.style.transition = "opacity 0.5s ease";
            alerta.style.opacity = "0";
            setTimeout(() => alerta.remove(), 500);
        });
    }, 10000);
</script>
@endpush

