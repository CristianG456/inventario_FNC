@extends('layouts.inventario')

@section('title', 'Inicio')

@section('content')

{{-- Alertas de Licencias --}}
@if(isset($alertasRojas) && $alertasRojas > 0)
<div class="alert alert-danger d-flex align-items-center mb-4 licencia-alerta" role="alert">
    <i class="bi bi-exclamation-octagon-fill fs-4 me-3"></i>
    <div>
        <strong>¡Atención!</strong> Hay {{ $alertasRojas }} licencia(s) vencida(s). <a href="{{ route('licencias.index', ['estado' => 'Vencida']) }}" class="alert-link">Ver licencias</a>.
    </div>
</div>
@endif

@if(isset($alertasAmarillas) && $alertasAmarillas > 0)
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
    
    <div class="dashboard-summary-total">
        <div class="dashboard-total-number">{{ $totalEquipos }}</div>
        <div class="dashboard-total-label">Equipos registrados</div>
    </div>
    
    <div class="dashboard-summary-divider"></div>
    
    <div class="dashboard-summary-states">
        <div class="dashboard-state-item">
            <div class="dashboard-state-icon active">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="dashboard-state-content">
                <div class="dashboard-state-label">Activos</div>
                <div class="dashboard-state-value active">{{ $activos }}</div>
            </div>
        </div>

        <div class="dashboard-state-item">
            <div class="dashboard-state-icon active">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="dashboard-state-content">
                <div class="dashboard-state-label">Disponibles</div>
                <div class="dashboard-state-value active">{{ $disponibles }}</div>
            </div>
        </div>

        <div class="dashboard-state-item">
            <div class="dashboard-state-icon inactive">
                <i class="bi bi-archive"></i>
            </div>
            <div class="dashboard-state-content">
                <div class="dashboard-state-label">Almacenados</div>
                <div class="dashboard-state-value inactive">{{ $almacenados }}</div>
            </div>
        </div>
        
        <div class="dashboard-state-item">
            <div class="dashboard-state-icon maintenance">
                <i class="bi bi-tools"></i>
            </div>
            <div class="dashboard-state-content">
                <div class="dashboard-state-label">En mantenimiento</div>
                <div class="dashboard-state-value maintenance">{{ $enMantenimiento }}</div>
            </div>
        </div>
        
        <div class="dashboard-state-item">
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
    {{-- Equipos por Tipo --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold border-0 pt-4 px-4">
                <i class="bi bi-pie-chart me-2 text-primary"></i>Equipos por Tipo
            </div>
            <div class="card-body px-4">
                @forelse($equiposPorTipo as $tipo)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-medium">{{ $tipo->nombre }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $tipo->equipos_count }}</span>
                        </div>
                        <div class="progress dashboard-progress-bar">
                            <div class="progress-bar"
                                 style="width: {{ $totalEquipos > 0 ? ($tipo->equipos_count / $totalEquipos * 100) : 0 }}%">
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Sin tipos de recurso registrados.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Últimos equipos --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2 text-primary"></i>Últimos Equipos Registrados</span>
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
                        <tbody>
                            @forelse($ultimosEquipos as $equipo)
                                <tr>
                                    <td>
                                        <a href="{{ route('equipos.show', $equipo) }}" class="text-decoration-none fw-medium">
                                            {{ $equipo->nombre_equipo }}
                                        </a>
                                        <br><small class="text-muted">{{ $equipo->serial }}</small>
                                    </td>
                                    <td>{{ $equipo->tipoRecurso?->nombre ?? '—' }}</td>
                                    <td>{{ $equipo->usuarioAsignado?->nombre ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $equipo->estado_badge }}">
                                            {{ $equipo->estado_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Sin equipos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    setTimeout(function() {
        const alertas = document.querySelectorAll('.licencia-alerta');
        alertas.forEach(alerta => {
            alerta.style.transition = "opacity 0.5s ease";
            alerta.style.opacity = "0";
            setTimeout(() => alerta.remove(), 500);
        });
    }, 40000);
</script>
@endpush

