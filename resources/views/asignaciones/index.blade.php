@extends('layouts.inventario')

@section('title', 'Asignaciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-person-fill-gear me-2 text-primary"></i>Historial de Asignaciones
    </h4>
</div>

{{-- Filtros --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('asignaciones.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-medium small mb-1">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           class="form-control" placeholder="Nombre, cédula, equipo, serial...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium small mb-1">Tipo de acción</label>
                <select name="tipo_accion" class="form-select">
                    <option value="">Todos</option>
                    @foreach($tiposAccion as $key => $label)
                        <option value="{{ $key }}" {{ request('tipo_accion') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
                <a href="{{ route('asignaciones.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Equipo</th>
                        <th>Usuario Asignado</th>
                        <th>Tipo Acción</th>
                        <th>Fecha</th>
                        <th>Registrado por</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $asignacion)
                        <tr>
                            <td class="text-muted small">{{ $asignacion->id }}</td>
                            <td>
                                <span class="fw-medium">{{ $asignacion->equipo?->nombre_equipo ?? '—' }}</span>
                                <br><small class="text-muted font-monospace">{{ $asignacion->equipo?->serial ?? '' }}</small>
                            </td>
                            <td>
                                @if($asignacion->usuario_nombre)
                                    {{ $asignacion->usuario_nombre }}
                                    <br><small class="text-muted">CC: {{ $asignacion->usuario_cedula }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $asignacion->tipo_accion_color }}">
                                    {{ $asignacion->tipo_accion_label }}
                                </span>
                            </td>
                            <td>
                                <span class="small">{{ $asignacion->fecha_accion?->format('d/m/Y H:i') ?? '—' }}</span>
                            </td>
                            <td class="small text-muted">{{ $asignacion->registradoPor?->name ?? '—' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('asignaciones.show', $asignacion) }}"
                                       class="btn btn-outline-info" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(in_array($asignacion->tipo_accion, ['asignacion','reemplazo']))
                                    <a href="{{ route('asignaciones.pdf', $asignacion) }}"
                                       class="btn btn-outline-danger" title="Descargar PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No hay registros de asignaciones.
                            </td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($asignaciones->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Mostrando {{ $asignaciones->firstItem() }}–{{ $asignaciones->lastItem() }} de {{ $asignaciones->total() }}
            </small>
            {{ $asignaciones->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
