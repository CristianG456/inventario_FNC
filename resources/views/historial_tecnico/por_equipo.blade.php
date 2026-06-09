@extends('layouts.inventario')

@section('title', 'Historial Técnico — ' . $equipo->nombre_equipo)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-tools me-2 text-warning"></i>Historial Técnico
        </h4>
        <small class="text-muted">{{ $equipo->nombre_equipo }} — Serial: {{ $equipo->serial }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('historial-tecnico.create', ['equipo_id' => $equipo->id]) }}"
           class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Evento
        </a>
        <a href="{{ route('equipos.show', $equipo) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

@if($registros->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-tools fs-2 d-block mb-2"></i>
            No hay eventos técnicos registrados para este equipo.
        </div>
    </div>
@else

{{-- Timeline --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <span><i class="bi bi-clock-history me-2 text-warning"></i><strong>Timeline de Eventos Técnicos</strong></span>
        <span class="badge bg-warning text-dark">{{ $registros->count() }} eventos</span>
    </div>
    <div class="card-body py-4">
        <div style="padding-left: 20px;">
            @foreach($registros as $registro)
            <div class="d-flex gap-3 mb-4 position-relative">
                @if(!$loop->last)
                <div style="position:absolute; left:19px; top:40px; bottom:-16px; width:2px; background:#e0e0e0;"></div>
                @endif

                {{-- Ícono --}}
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-{{ $registro->tipo_evento_color }} bg-opacity-15 border border-{{ $registro->tipo_evento_color }} d-flex align-items-center justify-content-center shadow-sm"
                         style="width:40px; height:40px; position:relative; z-index:1;">
                        <i class="bi {{ $registro->tipo_evento_icono }} text-{{ $registro->tipo_evento_color }}"></i>
                    </div>
                </div>

                {{-- Contenido --}}
                <div class="flex-grow-1">
                    <div class="card border-{{ $registro->tipo_evento_color }} border-opacity-25 shadow-sm">
                        <div class="card-body py-3 px-3">
                            {{-- Header --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-{{ $registro->tipo_evento_color }} me-2">
                                        {{ $registro->tipo_evento_label }}
                                    </span>
                                    <strong>{{ $registro->descripcion }}</strong>
                                </div>
                                <small class="text-muted text-nowrap ms-2">
                                    <i class="bi bi-calendar2 me-1"></i>
                                    {{ $registro->fecha_evento?->format('d \d\e F \d\e Y') }}
                                </small>
                            </div>

                            {{-- Motivo --}}
                            @if($registro->motivo)
                            <p class="text-muted small mb-2">
                                <i class="bi bi-chat-left-dots me-1"></i>
                                <strong>Motivo:</strong> {{ $registro->motivo }}
                            </p>
                            @endif

                            {{-- Responsable --}}
                            <p class="mb-2 small">
                                <i class="bi bi-person-fill me-1 text-muted"></i>
                                <strong>Responsable:</strong> {{ $registro->usuario_responsable }}
                            </p>

                            {{-- Usuario asignado en ese momento --}}
                            @if($registro->usuario_asignado_snapshot)
                            <div class="bg-light rounded p-2 mb-2 small">
                                <i class="bi bi-person-check me-1 text-success"></i>
                                <strong>Usuario asignado en ese momento:</strong>
                                {{ $registro->usuario_asignado_snapshot['nombre'] ?? '—' }}
                                (CC: {{ $registro->usuario_asignado_snapshot['cedula'] ?? '—' }})
                                @if(!empty($registro->usuario_asignado_snapshot['area']))
                                    — Área: {{ $registro->usuario_asignado_snapshot['area'] }}
                                @endif
                            </div>
                            @endif

                            {{-- Observaciones --}}
                            @if($registro->observaciones)
                            <p class="text-muted small mb-2">
                                <i class="bi bi-journal-text me-1"></i>{{ $registro->observaciones }}
                            </p>
                            @endif

                            {{-- Archivos --}}
                            @if($registro->archivos && count($registro->archivos) > 0)
                            <div class="mb-2">
                                @foreach($registro->archivos as $archivo)
                                <a href="{{ asset('storage/' . $archivo['ruta']) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-secondary me-1">
                                    <i class="bi bi-paperclip me-1"></i>{{ $archivo['nombre'] }}
                                </a>
                                @endforeach
                            </div>
                            @endif

                            {{-- Acciones --}}
                            <div class="d-flex gap-2 mt-2">
                                <a href="{{ route('historial-tecnico.show', $registro) }}"
                                   class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye me-1"></i>Ver
                                </a>
                                <a href="{{ route('historial-tecnico.edit', $registro) }}"
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil me-1"></i>Editar
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-delete-url="{{ route('historial-tecnico.destroy', $registro) }}"
                                        data-delete-name="el evento del {{ $registro->fecha_evento?->format('d/m/Y') }}">
                                    <i class="bi bi-trash me-1"></i>Eliminar
                                </button>
                                <small class="text-muted ms-auto align-self-center">
                                    Registrado por: {{ $registro->registradoPor?->name ?? '—' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
