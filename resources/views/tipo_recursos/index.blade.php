@extends('layouts.inventario')

@section('title', 'Tipos de Recurso')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h4 class="fw-bold mb-0"><i class="bi bi-tags me-2 text-primary"></i>Tipos de Recurso</h4>
    <a href="#" class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalCatalogo">
        <i class="bi bi-box-seam me-1"></i>Catálogo Complementos
    </a>
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
                        <th class="text-center">Complementos</th>
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
                                <span class="badge bg-info text-white rounded-pill">{{ $tipo->complementosDefinidos->count() }}</span>
                            </td>
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

<!-- Modal Catálogo Complementos -->
<div class="modal fade" id="modalCatalogo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-box-seam me-2"></i>Catálogo de Complementos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCatalogo" action="{{ route('catalogo-complementos.store') }}" method="POST" class="mb-4 bg-light p-3 border rounded shadow-sm">
                    @csrf
                    <input type="hidden" name="_method" id="methodCatalogo" value="POST">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small">Complemento</label>
                            <input type="text" name="nombre" id="nombreCatalogo" class="form-control form-control-sm" required placeholder="Ej: Cargador, Batería...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Compatible con</label>
                            <select name="tipo_recursos_ids[]" id="tipoRecursosCatalogo" class="form-select form-select-sm" multiple required style="height: 60px;">
                                @foreach(\App\Models\TipoRecurso::orderBy('nombre')->get() as $tr)
                                    <option value="{{ $tr->id }}">{{ $tr->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <div class="form-check pt-4">
                                <input class="form-check-input" type="checkbox" name="requiere_serial" value="1" id="reqSerial">
                                <label class="form-check-label small" for="reqSerial">Serial</label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-check pt-4">
                                <input class="form-check-input" type="checkbox" name="usa_estado" value="1" id="usaEstado" checked>
                                <label class="form-check-label small" for="usaEstado">Estado</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="activo" value="1" id="activoCatalogo" checked>
                                <label class="form-check-label small" for="activoCatalogo">Activo</label>
                            </div>
                        </div>
                        <div class="col-md-2 text-end pb-1 d-flex flex-column gap-1">
                            <button type="submit" id="btnSubmitCatalogo" class="btn btn-sm btn-info text-white w-100">Guardar</button>
                            <button type="button" id="btnCancelCatalogo" class="btn btn-sm btn-secondary w-100 d-none" onclick="resetCatalogoForm()">Cancelar</button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 text-muted small">
                            <i class="bi bi-info-circle me-1"></i> Mantén presionada la tecla Ctrl (o Cmd) para seleccionar múltiples tipos de recurso.
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-hover border">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Compatible con</th>
                                <th class="text-center">Req. Serial</th>
                                <th class="text-center">Usa Estado</th>
                                <th class="text-center">Activo</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($catalogoComplementos ?? [] as $comp)
                                <tr>
                                    <td class="fw-medium">{{ $comp->nombre }}</td>
                                    <td>
                                        @if($comp->tipoRecursos->count() > 0)
                                            <span class="text-muted small">{{ $comp->tipoRecursos->pluck('nombre')->join(', ') }}</span>
                                        @else
                                            <span class="text-danger small">Ninguno</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{!! $comp->requiere_serial ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                                    <td class="text-center">{!! $comp->usa_estado ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                                    <td class="text-center">{!! $comp->activo ? '<span class="badge bg-primary">Sí</span>' : '<span class="badge bg-danger">No</span>' !!}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                            onclick="editCatalogo({{ $comp->id }}, '{{ $comp->nombre }}', {{ json_encode($comp->tipoRecursos->pluck('id')) }}, {{ $comp->requiere_serial ? 1 : 0 }}, {{ $comp->usa_estado ? 1 : 0 }}, {{ $comp->activo ? 1 : 0 }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">No hay complementos en el catálogo</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-control { min-height: 38px; padding: 6px 12px; }
    .ts-control > input { padding: 0 !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    let tomSelectInstance;
    document.addEventListener('DOMContentLoaded', function() {
        tomSelectInstance = new TomSelect('#tipoRecursosCatalogo', {
            plugins: ['remove_button'],
            placeholder: 'Selecciona tipos de recurso...',
            searchField: ['text'],
            hideSelected: true
        });
    });

    const urlStore = "{{ route('catalogo-complementos.store') }}";
    function editCatalogo(id, nombre, tipoRecursos, reqSerial, usaEstado, activo) {
        document.getElementById('formCatalogo').action = `/catalogo-complementos/${id}`;
        document.getElementById('methodCatalogo').value = 'PUT';
        document.getElementById('nombreCatalogo').value = nombre;
        document.getElementById('reqSerial').checked = reqSerial === 1;
        document.getElementById('usaEstado').checked = usaEstado === 1;
        document.getElementById('activoCatalogo').checked = activo === 1;
        
        if (tomSelectInstance) {
            tomSelectInstance.setValue(tipoRecursos);
        }

        document.getElementById('btnSubmitCatalogo').textContent = 'Actualizar';
        document.getElementById('btnCancelCatalogo').classList.remove('d-none');
    }

    function resetCatalogoForm() {
        document.getElementById('formCatalogo').action = urlStore;
        document.getElementById('methodCatalogo').value = 'POST';
        document.getElementById('formCatalogo').reset();
        
        if (tomSelectInstance) {
            tomSelectInstance.clear();
        }

        document.getElementById('btnSubmitCatalogo').textContent = 'Guardar';
        document.getElementById('btnCancelCatalogo').classList.add('d-none');
    }
</script>
@endpush
