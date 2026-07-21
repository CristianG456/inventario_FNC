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
            <div class="search-bar flex-grow-1 funcionario-search-bar">
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
                    <th>Autorizaciones</th>
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
                        <span class="badge {{ $func->equipos_asignados_count > 0 ? 'bg-primary' : 'bg-light text-dark border' }}">
                            <i class="bi bi-laptop me-1"></i> {{ $func->equipos_asignados_count }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $func->autorizaciones_disponibles_count > 0 ? 'bg-success' : 'bg-light text-dark border' }}" title="Disponibles para asignar">
                            <i class="bi bi-file-earmark-check me-1"></i> {{ $func->autorizaciones_disponibles_count }}
                        </span>
                        <span class="badge bg-light text-dark border" title="Total históricas">
                            T: {{ $func->autorizaciones_total_count }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('funcionarios.show', $func) }}" class="btn btn-sm btn-light rounded-circle" title="Ver funcionario">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('funcionarios.edit', $func) }}" class="btn btn-sm btn-light rounded-circle" title="Editar funcionario">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button"
                                class="btn btn-sm btn-outline-primary rounded-circle ms-1 btn-subir-autorizacion"
                                data-funcionario-id="{{ $func->id }}"
                                data-funcionario-nombre="{{ $func->nombres }} {{ $func->apellidos }}"
                                data-funcionario-cedula="{{ $func->identificacion }}"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Subir autorización de {{ trim($func->nombres . ' ' . $func->apellidos) }}">
                            <i class="bi bi-upload"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
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

<div class="modal fade" id="modalAutorizacionFuncionario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formAutorizacionFuncionario" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Subir autorización de activo adicional</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Funcionario: <strong id="autorizacionFuncionarioNombre"></strong><br>
                        Cédula: <strong id="autorizacionFuncionarioCedula"></strong>
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Archivo firmado <span class="text-danger">*</span></label>
                        <input type="file" name="archivo_autorizacion" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp" required>
                        <small class="text-muted">Formatos permitidos: PDF, JPG, PNG, WEBP. Máx. 10 MB.</small>
                    </div>

                    <div>
                        <label class="form-label fw-medium">Observaciones</label>
                        <textarea name="observaciones" rows="2" class="form-control" maxlength="1000"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Guardar autorización
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
        new bootstrap.Tooltip(el);
    });

    const modalEl = document.getElementById('modalAutorizacionFuncionario');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const form = document.getElementById('formAutorizacionFuncionario');
    const nombreEl = document.getElementById('autorizacionFuncionarioNombre');
    const cedulaEl = document.getElementById('autorizacionFuncionarioCedula');

    document.querySelectorAll('.btn-subir-autorizacion').forEach((btn) => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-funcionario-id');
            const nombre = this.getAttribute('data-funcionario-nombre') || '';
            const cedula = this.getAttribute('data-funcionario-cedula') || '';

            if (!form) return;

            form.action = `/funcionarios/${id}/autorizaciones`;
            form.reset();
            if (nombreEl) nombreEl.textContent = nombre.trim();
            if (cedulaEl) cedulaEl.textContent = cedula;

            if (modal) {
                modal.show();
            }
        });
    });
});
</script>
@endpush
@endsection
