@extends('layouts.inventario')

@section('title', 'Gestión de Funcionarios')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title">Gestión de Funcionarios</h4>
        <p class="page-subtitle">Directorio y control administrativo del personal ({{ $funcionarios->total() }} registros)</p>
    </div>
    <a href="{{ route('funcionarios.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Funcionario
    </a>
</div>

<div class="card p-0">
    <div class="p-4 border-bottom border-light">
        <form action="{{ route('funcionarios.index') }}" method="GET" class="d-flex gap-3 align-items-center">
            <div class="search-bar flex-grow-1" style="max-width: 500px;">
                <i class="bi bi-search text-muted"></i>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Busca por nombre, cédula, cargo...">
            </div>
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-funnel me-1"></i> Más Filtros
            </button>
        </form>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="ps-4">Funcionario</th>
                    <th>Identificación</th>
                    <th>Gestión / Área</th>
                    <th>Vinculación</th>
                    <th>Activos</th>
                    <th class="text-end pe-4">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($funcionarios as $func)
                <tr>
                    <td class="ps-4 fw-medium">{{ $func->nombres }} {{ $func->apellidos }}</td>
                    <td class="text-muted">{{ $func->identificacion }}</td>
                    <td>
                        <div>{{ $func->cargo ?? '—' }}</div>
                        <small class="text-muted">{{ $func->area ?? '—' }}</small>
                    </td>
                    <td>
                        <span class="badge {{ $func->estado == 'Activo' ? 'badge-success' : 'badge-secondary' }}">
                            {{ $func->estado }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-laptop me-1"></i> 0
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-light rounded-circle"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-light rounded-circle"><i class="bi bi-pencil"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        NO SE ENCONTRARON FUNCIONARIOS REGISTRADOS
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($funcionarios->hasPages())
    <div class="p-3 border-top">
        {{ $funcionarios->links() }}
    </div>
    @endif
</div>
@endsection
