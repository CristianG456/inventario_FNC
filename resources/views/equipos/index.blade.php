@extends('layouts.inventario')

@section('title', 'Equipos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-laptop me-2 text-primary"></i>Inventario de Equipos</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('equipos.exportar') }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
        </a>
        <a href="{{ route('equipos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Equipo
        </a>
    </div>
</div>

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('equipos.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-medium small mb-1">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           class="form-control" placeholder="Serial, nombre, marca, usuario...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium small mb-1">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos los tipos</option>
                    @foreach($tipoRecursos as $tipo)
                        <option value="{{ $tipo->id }}" {{ request('tipo') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium small mb-1">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="mantenimiento" {{ request('estado') === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    <option value="baja" {{ request('estado') === 'baja' ? 'selected' : '' }}>Baja</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
                <a href="{{ route('equipos.index') }}" class="btn btn-outline-secondary">
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
                        <th>Equipo / Serial</th>
                        <th>Tipo</th>
                        <th>Marca / Modelo</th>
                        <th>Usuario Asignado</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipos as $equipo)
                        <tr>
                            <td class="text-muted small">{{ $equipo->id }}</td>
                            <td>
                                <span class="fw-medium">{{ $equipo->nombre_equipo }}</span>
                                <br><small class="text-muted font-monospace">{{ $equipo->serial }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-75">
                                    {{ $equipo->tipoRecurso?->nombre ?? '—' }}
                                </span>
                            </td>
                            <td>
                                {{ $equipo->marca }}
                                <br><small class="text-muted">{{ $equipo->modelo }}</small>
                            </td>
                            <td>
                                @if($equipo->usuarioAsignado)
                                    {{ $equipo->usuarioAsignado->nombre }}
                                    <br><small class="text-muted">CC: {{ $equipo->usuarioAsignado->cedula }}</small>
                                @else
                                    <span class="text-muted">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $equipo->estado_badge }}">
                                    {{ $equipo->estado_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('equipos.show', $equipo) }}"
                                       class="btn btn-outline-info" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('equipos.edit', $equipo) }}"
                                       class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-outline-danger"
                                            title="Eliminar"
                                            data-delete-url="{{ route('equipos.destroy', $equipo) }}"
                                            data-delete-name="{{ $equipo->nombre_equipo }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No hay equipos que coincidan con los filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($equipos->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Mostrando {{ $equipos->firstItem() }}–{{ $equipos->lastItem() }} de {{ $equipos->total() }} equipos
            </small>
            {{ $equipos->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
