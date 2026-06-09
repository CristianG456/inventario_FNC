@extends('layouts.inventario')

@section('title', 'Historial de Vida — ' . $equipo->nombre_equipo)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-clock-history me-2 text-primary"></i>
            Historial de Vida
        </h4>
        <small class="text-muted">{{ $equipo->nombre_equipo }} — Serial: {{ $equipo->serial }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('equipos.show', $equipo) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al equipo
        </a>
    </div>
</div>

{{-- Resumen en tarjetas --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-15 d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                    <i class="bi bi-person-fill text-success fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5">{{ $eventos->where('tipo', 'asignacion')->count() }}</div>
                    <div class="text-muted small">Eventos de asignación</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-15 d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                    <i class="bi bi-tools text-warning fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5">{{ $eventos->where('tipo', 'tecnico')->count() }}</div>
                    <div class="text-muted small">Eventos técnicos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-15 d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                    <i class="bi bi-shield-check text-info fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5">{{ $eventos->where('tipo', 'administrativo')->count() }}</div>
                    <div class="text-muted small">Cambios administrativos</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Timeline --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
        <i class="bi bi-clock-history text-primary"></i>
        <strong>Línea de Tiempo Completa</strong>
        <span class="badge bg-secondary ms-auto">{{ $eventos->count() }} eventos</span>
    </div>
    <div class="card-body">
        @if($eventos->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                No hay eventos registrados para este equipo.
            </div>
        @else
        <div class="timeline-container" style="padding-left: 20px;">
            @foreach($eventos as $index => $evento)
            @php
                $fecha = is_string($evento['fecha']) ? \Carbon\Carbon::parse($evento['fecha']) : $evento['fecha'];
            @endphp
            <div class="timeline-item d-flex gap-3 mb-4 position-relative">
                {{-- Línea vertical --}}
                @if(!$loop->last)
                <div style="position:absolute; left:19px; top:38px; bottom:-16px; width:2px; background:#e0e0e0; z-index:0;"></div>
                @endif

                {{-- Icono --}}
                <div class="flex-shrink-0 position-relative z-1">
                    <div class="rounded-circle bg-{{ $evento['color'] }} bg-opacity-15 border border-{{ $evento['color'] }} d-flex align-items-center justify-content-center shadow-sm"
                         style="width:40px; height:40px;">
                        <i class="bi {{ $evento['icono'] }} text-{{ $evento['color'] }}"></i>
                    </div>
                </div>

                {{-- Contenido --}}
                <div class="flex-grow-1">
                    <div class="card border-{{ $evento['color'] }} border-opacity-25 shadow-sm">
                        <div class="card-body py-3 px-3">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <span class="badge bg-{{ $evento['color'] }} bg-opacity-75 me-2">
                                        {{ ucfirst($evento['tipo']) }}
                                    </span>
                                    <strong class="fs-6">{{ $evento['titulo'] }}</strong>
                                </div>
                                <small class="text-muted text-nowrap ms-2">
                                    <i class="bi bi-calendar2 me-1"></i>
                                    {{ $fecha->format('d/m/Y') }}
                                    @if($evento['tipo'] === 'administrativo' || $evento['tipo'] === 'asignacion')
                                        {{ $fecha->format('H:i') }}
                                    @endif
                                </small>
                            </div>

                            @if($evento['descripcion'])
                            <p class="mb-1 text-secondary small">{{ $evento['descripcion'] }}</p>
                            @endif

                            @if($evento['responsable'])
                            <small class="text-muted">
                                <i class="bi bi-person me-1"></i>{{ $evento['responsable'] }}
                            </small>
                            @endif

                            {{-- Detalle adicional según tipo --}}
                            @if($evento['tipo'] === 'asignacion' && $evento['modelo']->motivo)
                            <div class="mt-1">
                                <small class="text-muted"><i class="bi bi-chat-left-text me-1"></i>{{ $evento['modelo']->motivo }}</small>
                            </div>
                            @endif

                            {{-- Enlace al detalle --}}
                            <div class="mt-2">
                                @if($evento['tipo'] === 'asignacion')
                                    <a href="{{ route('asignaciones.show', $evento['modelo']->id) }}"
                                       class="btn btn-sm btn-outline-{{ $evento['color'] }}">
                                        <i class="bi bi-eye me-1"></i>Ver detalle
                                    </a>
                                @elseif($evento['tipo'] === 'tecnico')
                                    <a href="{{ route('historial-tecnico.show', $evento['modelo']->id) }}"
                                       class="btn btn-sm btn-outline-{{ $evento['color'] }}">
                                        <i class="bi bi-eye me-1"></i>Ver detalle
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
