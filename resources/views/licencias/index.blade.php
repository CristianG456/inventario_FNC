@extends('layouts.inventario')

@section('title', 'Licencias')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-key me-2 text-primary"></i>Gestión de Licencias</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('licencias.exportar', request()->query()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
        </a>
        <a href="{{ route('licencia-asignaciones.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-list-check me-1"></i>Ver Asignaciones
        </a>
        <a href="{{ route('licencias.historial') }}" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i>Historial
        </a>
        <a href="{{ route('licencias.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nueva Licencia
        </a>
    </div>
</div>

{{-- Alertas --}}
@if($alertasRojas->count() > 0)
<div class="alert alert-danger d-flex align-items-center licencia-alerta" role="alert">
    <i class="bi bi-exclamation-octagon-fill fs-4 me-3"></i>
    <div>
        <strong>¡Atención!</strong> Hay {{ $alertasRojas->count() }} licencia(s) vencida(s).
    </div>
</div>
@endif

@if($alertasAmarillas->count() > 0)
<div class="alert alert-warning d-flex align-items-center licencia-alerta" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
    <div>
        <strong>Aviso:</strong> Hay {{ $alertasAmarillas->count() }} licencia(s) por vencer en los próximos 30 días.
    </div>
</div>
@endif

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('licencias.index') }}" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-medium small mb-1">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           class="form-control" placeholder="Nombre, tipo, estado...">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium small mb-1">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="Activa" {{ request('estado') === 'Activa' ? 'selected' : '' }}>Activa</option>
                    <option value="Suspendida" {{ request('estado') === 'Suspendida' ? 'selected' : '' }}>Suspendida</option>
                    <option value="Vencida" {{ request('estado') === 'Vencida' ? 'selected' : '' }}>Vencida</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
                <a href="{{ route('licencias.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Cupos</th>
                        <th>Fechas</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licencias as $licencia)
                        <tr>
                            <td class="text-muted small">{{ $licencia->id }}</td>
                            <td>
                                <span class="fw-medium">{{ $licencia->nombre }}</span>
                            </td>
                            <td>
                                {{ $licencia->tipo_licencia }}
                            </td>
                            <td>
                                <span class="badge {{ $licencia->cupos_disponibles > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $licencia->cupos_asignados }} / {{ $licencia->cantidad_maxima }} Asignados
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">Inicio:</small> {{ $licencia->fecha_inicio ? $licencia->fecha_inicio->format('d/m/Y') : 'N/A' }}<br>
                                <small class="text-muted">Vence:</small> <strong class="{{ $licencia->fecha_vencimiento && $licencia->fecha_vencimiento->isPast() ? 'text-danger' : '' }}">{{ $licencia->fecha_vencimiento ? $licencia->fecha_vencimiento->format('d/m/Y') : 'N/A' }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $licencia->estado === 'Activa' ? 'success' : ($licencia->estado === 'Suspendida' ? 'warning' : 'danger') }}">
                                    {{ $licencia->estado }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <a href="{{ route('licencias.show', $licencia) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('licencias.edit', $licencia) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                            data-delete-url="{{ route('licencias.destroy', $licencia) }}"
                                            data-delete-name="{{ $licencia->nombre }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No hay licencias registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($licencias->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Mostrando {{ $licencias->firstItem() }}–{{ $licencias->lastItem() }} de {{ $licencias->total() }} licencias
            </small>
            {{ $licencias->links('pagination::bootstrap-5') }}
        </div>
    @endif
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
