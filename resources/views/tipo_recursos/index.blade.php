@extends('layouts.inventario')

@section('title', 'Tipos de Recurso')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-tags me-2 text-primary"></i>Tipos de Recurso</h4>
    <a href="{{ route('tipo-recursos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Tipo
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th class="text-center">N&deg; Equipos</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tipoRecursos as $tipo)
                        <tr>
                            <td class="text-muted small">{{ $tipo->id }}</td>
                            <td class="fw-medium">{{ $tipo->nombre }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill">{{ $tipo->equipos_count }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('tipo-recursos.edit', $tipo) }}"
                                       class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-outline-danger"
                                            title="Eliminar"
                                            data-delete-url="{{ route('tipo-recursos.destroy', $tipo) }}"
                                            data-delete-name="{{ $tipo->nombre }}"
                                            data-count="{{ $tipo->equipos_count }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No hay tipos de recurso registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tipoRecursos->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $tipoRecursos->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
