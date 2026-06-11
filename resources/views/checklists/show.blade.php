@extends('layouts.inventario')

@section('title', 'Detalle Checklist')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-clipboard-check me-2 text-info"></i>Detalle de Checklist
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('checklists.edit', $checklist) }}" class="btn btn-warning text-white">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <a href="{{ route('checklists.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card config-card-medium">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-4 text-muted">Equipo</dt>
            <dd class="col-sm-8">
                @if($checklist->equipo)
                    <a href="{{ route('equipos.show', $checklist->equipo) }}">
                        {{ $checklist->equipo->nombre_equipo }}
                    </a>
                    <span class="text-muted ms-2">({{ $checklist->equipo->serial }})</span>
                @else
                    —
                @endif
            </dd>
            <dt class="col-sm-4 text-muted">Responsable TI</dt>
            <dd class="col-sm-8">{{ $checklist->responsable_ti ?? '—' }}</dd>
            <dt class="col-sm-4 text-muted">Orden de Trabajo</dt>
            <dd class="col-sm-8">{{ $checklist->orden_trabajo ?? '—' }}</dd>
            <dt class="col-sm-4 text-muted">Cruce AV</dt>
            <dd class="col-sm-8">{{ $checklist->cruce_av ?? '—' }}</dd>
            <dt class="col-sm-4 text-muted">Cruce Short name</dt>
            <dd class="col-sm-8">{{ $checklist->crece_software ?? '—' }}</dd>
            <dt class="col-sm-4 text-muted">Resultado Cruce Antivirus</dt>
            <dd class="col-sm-8">{{ $checklist->resultado ?? '—' }}</dd>
            <dt class="col-sm-4 text-muted">Tipo Aprobado</dt>
            <dd class="col-sm-8">{{ $checklist->tipo_aprobado ?? '—' }}</dd>
            <dt class="col-sm-4 text-muted">FNC</dt>
            <dd class="col-sm-8">{{ $checklist->fnc ?? '—' }}</dd>
            <dt class="col-sm-4 text-muted">Observaciones</dt>
            <dd class="col-sm-8">{{ $checklist->observaciones ?? '—' }}</dd>
            <dt class="col-sm-4 text-muted">Registrado el</dt>
            <dd class="col-sm-8">{{ $checklist->created_at->format('d/m/Y H:i') }}</dd>
        </dl>
    </div>
</div>
@endsection
