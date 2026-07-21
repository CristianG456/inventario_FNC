@extends('layouts.inventario')

@section('title', 'Detalle — Evento Técnico')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi {{ $historialTecnico->tipo_evento_icono }} me-2 text-{{ $historialTecnico->tipo_evento_color }}"></i>
            {{ $historialTecnico->tipo_evento_label }}
        </h4>
        <small class="text-muted">
            {{ $historialTecnico->equipo?->nombre_equipo }} — {{ $historialTecnico->fecha_evento?->format('d/m/Y') }}
        </small>
    </div>
    <div class="d-flex gap-2">
        @if($puedeModificarBitacora)
            <a href="{{ route('historial-tecnico.edit', $historialTecnico) }}" class="btn btn-warning text-white">
                <i class="bi bi-pencil me-1"></i>Editar
            </a>
        @endif
        <a href="{{ route('historial-tecnico.por-equipo', ['equipo' => $historialTecnico->equipo_id, 'return_to' => request()->fullUrl()]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i>Historial del Equipo
        </a>
        <a href="{{ route('historial-tecnico.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-{{ $historialTecnico->tipo_evento_color }} bg-opacity-10 fw-semibold border-0 py-3">
                <i class="bi {{ $historialTecnico->tipo_evento_icono }} me-2 text-{{ $historialTecnico->tipo_evento_color }}"></i>
                Detalle del Evento
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Tipo de Evento</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $historialTecnico->tipo_evento_color }}">
                            {{ $historialTecnico->tipo_evento_label }}
                        </span>
                    </dd>
                    <dt class="col-sm-4 text-muted">Observaciones</dt>
                    <dd class="col-sm-8">{{ $historialTecnico->observaciones ?: $historialTecnico->descripcion }}</dd>
                    <dt class="col-sm-4 text-muted">Fecha del Evento</dt>
                    <dd class="col-sm-8">{{ $historialTecnico->fecha_evento?->format('d \d\e F \d\e Y') }}</dd>
                    <dt class="col-sm-4 text-muted">Técnico Responsable</dt>
                    <dd class="col-sm-8 fw-bold">{{ $historialTecnico->usuario_responsable_label }}</dd>
                    <dt class="col-sm-4 text-muted">Registrado por</dt>
                    <dd class="col-sm-8">{{ $historialTecnico->registradoPor?->name ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Fecha de Registro</dt>
                    <dd class="col-sm-8">{{ $historialTecnico->created_at?->format('d/m/Y H:i') }}</dd>
                </dl>

                {{-- Archivos adjuntos --}}
                @if($historialTecnico->archivos && count($historialTecnico->archivos) > 0)
                <hr>
                <h6 class="fw-bold mb-3"><i class="bi bi-paperclip me-1"></i>Archivos Adjuntos</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($historialTecnico->archivos as $archivo)
                    <a href="{{ asset('storage/' . $archivo['ruta']) }}"
                       target="_blank"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-file-earmark me-1"></i>{{ $archivo['nombre'] }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Usuario asignado en ese momento --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success bg-opacity-10 fw-semibold border-0 py-3">
                <i class="bi bi-person-check me-2 text-success"></i>Usuario en ese Momento
            </div>
            <div class="card-body">
                @if($historialTecnico->usuario_asignado_snapshot)
                @php $snap = $historialTecnico->usuario_asignado_snapshot; @endphp
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Nombre</dt>
                    <dd class="col-7 fw-bold">{{ $snap['nombre'] ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Cédula</dt>
                    <dd class="col-7">{{ $snap['cedula'] ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Cargo</dt>
                    <dd class="col-7">{{ $snap['cargo'] ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Área</dt>
                    <dd class="col-7">{{ $snap['area'] ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Dependencia</dt>
                    <dd class="col-7">{{ $snap['dependencia'] ?? '—' }}</dd>
                    @if(!empty($snap['distrito']))
                    <dt class="col-5 text-muted">Distrito</dt>
                    <dd class="col-7">{{ $snap['distrito'] }}</dd>
                    @endif
                    @if(!empty($snap['seccional']))
                    <dt class="col-5 text-muted">Seccional</dt>
                    <dd class="col-7">{{ $snap['seccional'] }}</dd>
                    @endif
                </dl>
                @else
                <p class="text-muted mb-0 small">No se registró usuario asignado en este momento.</p>
                @endif
            </div>
        </div>

        {{-- Datos del equipo --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-primary bg-opacity-10 fw-semibold border-0 py-3">
                <i class="bi bi-laptop me-2 text-primary"></i>Equipo
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Nombre</dt>
                    <dd class="col-7 fw-bold">{{ $historialTecnico->equipo?->nombre_equipo }}</dd>
                    <dt class="col-5 text-muted">Serial</dt>
                    <dd class="col-7 font-monospace">{{ $historialTecnico->equipo?->serial }}</dd>
                    @if($historialTecnico->equipo?->activo_fijo)
                    <dt class="col-5 text-muted">Activo Fijo</dt>
                    <dd class="col-7">{{ $historialTecnico->equipo->activo_fijo }}</dd>
                    @endif
                </dl>
                <a href="{{ route('equipos.show', $historialTecnico->equipo_id) }}"
                   class="btn btn-sm btn-outline-primary mt-2 w-100">
                    <i class="bi bi-eye me-1"></i>Ver Equipo
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
