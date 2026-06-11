@extends('layouts.inventario')

@section('title', 'Dashboard')

@section('content')

{{-- Alertas de Licencias --}}
@if(isset($alertasRojas) && $alertasRojas->count() > 0)
<div class="alert alert-danger d-flex align-items-center mb-4 licencia-alerta" role="alert">
    <i class="bi bi-exclamation-octagon-fill fs-4 me-3"></i>
    <div>
        <strong>¡Atención!</strong> Hay {{ $alertasRojas->count() }} licencia(s) vencida(s). <a href="{{ route('licencias.index', ['estado' => 'Vencida']) }}" class="alert-link">Ver licencias</a>.
    </div>
</div>
@endif

@if(isset($alertasAmarillas) && $alertasAmarillas->count() > 0)
<div class="alert alert-warning d-flex align-items-center mb-4 licencia-alerta" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
    <div>
        <strong>Aviso:</strong> Hay {{ $alertasAmarillas->count() }} licencia(s) por vencer en los próximos 30 días. <a href="{{ route('licencias.index') }}" class="alert-link">Revisar licencias</a>.
    </div>
</div>
@endif

<div class="row g-4 mb-4">
    {{-- Tarjeta: Total --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card p-4 text-center border-start border-primary border-4">
            <div class="fs-1 fw-bold text-primary">{{ $totalEquipos }}</div>
            <div class="text-muted mt-1"><i class="bi bi-laptop me-1"></i>Total de Equipos</div>
        </div>
    </div>
    {{-- Tarjeta: Activos --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card p-4 text-center border-start border-success border-4">
            <div class="fs-1 fw-bold text-success">{{ $activos }}</div>
            <div class="text-muted mt-1"><i class="bi bi-check-circle me-1"></i>Activos</div>
        </div>
    </div>
    {{-- Tarjeta: Mantenimiento --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card p-4 text-center border-start border-warning border-4">
            <div class="fs-1 fw-bold text-warning">{{ $enMantenimiento }}</div>
            <div class="text-muted mt-1"><i class="bi bi-tools me-1"></i>En Mantenimiento</div>
        </div>
    </div>
    {{-- Tarjeta: Baja --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card p-4 text-center border-start border-danger border-4">
            <div class="fs-1 fw-bold text-danger">{{ $deBaja }}</div>
            <div class="text-muted mt-1"><i class="bi bi-x-circle me-1"></i>De Baja</div>
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

