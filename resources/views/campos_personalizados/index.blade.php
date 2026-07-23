@extends('layouts.inventario')

@section('title', 'Campos Personalizados')

@push('styles')
<style>
    .sortable-ghost {
        opacity: 0.4;
        background-color: #f8f9fa;
    }
    .handle {
        cursor: grab;
    }
    .handle:active {
        cursor: grabbing;
    }
    .opciones-wrapper {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h4 class="fw-bold mb-0"><i class="bi bi-ui-checks-grid me-2 text-primary"></i>Campos Personalizados</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCampo" onclick="openCreateModal()">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Campo
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="camposTable">
                <thead class="table-light">
                    <tr>
                        <th width="50"></th>
                        <th>Módulo</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Opciones de Config.</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="sortable-list">
                    @forelse($campos as $campo)
                        <tr data-id="{{ $campo->id }}">
                            <td class="text-center text-muted handle"><i class="bi bi-grip-vertical fs-5"></i></td>
                            <td><span class="badge bg-secondary">{{ strtoupper($campo->modulo) }}</span></td>
                            <td>
                                <span class="fw-bold d-block">{{ $campo->nombre }}</span>
                                <small class="text-muted">{{ Str::limit($campo->descripcion, 50) }}</small>
                            </td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($campo->tipo) }}</span></td>
                            <td>
                                @if($campo->obligatorio) <span class="badge bg-danger ms-1" title="Obligatorio">OBL</span> @endif
                                @if($campo->visible) <span class="badge bg-success ms-1" title="Visible">VIS</span> @endif
                                @if($campo->importable) <span class="badge bg-primary ms-1" title="Importable">IMP</span> @endif
                                @if($campo->exportable) <span class="badge bg-warning text-dark ms-1" title="Exportable">EXP</span> @endif
                            </td>
                            <td>
                                @if($campo->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="openEditModal({{ $campo->toJson() }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('campos-personalizados.destroy', $campo->id) }}" method="POST" style="display:inline-block;" class="form-eliminar">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-ui-radios fs-2 d-block mb-2"></i>No hay campos personalizados registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create/Edit -->
<div class="modal fade" id="modalCampo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-md-down">
        <form id="formCampo" method="POST" action="{{ route('campos-personalizados.store') }}">
            @csrf
            <input type="hidden" name="_method" id="methodCampo" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Campo Personalizado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Módulo *</label>
                            <select name="modulo" id="modulo" class="form-select" required>
                                <option value="equipos">Activos</option>
                                <option value="licencias">Licencias</option>
                                <option value="suscripciones">Suscripciones</option>
                                <option value="vitalicias">Vitalicias</option>
                                <option value="actas">Actas</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Tipo de Campo *</label>
                            <select name="tipo" id="tipo" class="form-select" required onchange="toggleOpciones()">
                                <option value="texto">Texto corto</option>
                                <option value="textarea">Texto largo (Textarea)</option>
                                <option value="numero">Número</option>
                                <option value="fecha">Fecha</option>
                                <option value="correo">Correo Electrónico</option>
                                <option value="telefono">Teléfono</option>
                                <option value="boolean">Sí / No (Boolean)</option>
                                <option value="select">Selección Única (Select)</option>
                                <option value="multiselect">Selección Múltiple (Multiselect)</option>
                                <option value="url">URL</option>
                            </select>
                        </div>
                        
                        <div class="col-12 col-md-12">
                            <label class="form-label">Nombre del Campo *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Ej: Centro de Costos">
                        </div>
                        
                        <div class="col-12 col-md-12 opciones-wrapper">
                            <label class="form-label text-primary">Opciones de la Lista</label>
                            <textarea name="opciones" id="opciones" class="form-control" rows="3" placeholder="Opción A, Opción B, Opción C... (Separadas por comas)"></textarea>
                            <small class="text-muted">Ingresa las opciones separadas por comas.</small>
                        </div>
                        
                        <div class="col-12"><hr></div>
                        
                        <div class="col-12 col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="obligatorio" id="obligatorio" value="1">
                                <label class="form-check-label" for="obligatorio">Obligatorio</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="visible" id="visible" value="1" checked>
                                <label class="form-check-label" for="visible">Visible</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="editable" id="editable" value="1" checked>
                                <label class="form-check-label" for="editable">Editable</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="importable" id="importable" value="1" checked>
                                <label class="form-check-label" for="importable">Importable CMDB</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="exportable" id="exportable" value="1" checked>
                                <label class="form-check-label" for="exportable">Exportable Excel</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="exportar_por_defecto" id="exportar_por_defecto" value="1">
                                <label class="form-check-label" for="exportar_por_defecto">Exportar por defecto</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="activo" id="activo" value="1" checked>
                                <label class="form-check-label" for="activo">Activo</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar Campo</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    function toggleOpciones() {
        const tipo = document.getElementById('tipo').value;
        const wrapper = document.querySelector('.opciones-wrapper');
        if (tipo === 'select' || tipo === 'multiselect') {
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
        }
    }

    function openCreateModal() {
        document.getElementById('formCampo').reset();
        document.getElementById('formCampo').action = "{{ route('campos-personalizados.store') }}";
        document.getElementById('methodCampo').value = "POST";
        document.getElementById('modalTitle').innerText = "Nuevo Campo Personalizado";
        toggleOpciones();
    }

    function openEditModal(campo) {
        document.getElementById('formCampo').action = `/campos-personalizados/${campo.id}`;
        document.getElementById('methodCampo').value = "PUT";
        document.getElementById('modalTitle').innerText = "Editar Campo Personalizado";
        
        document.getElementById('modulo').value = campo.modulo;
        document.getElementById('tipo').value = campo.tipo;
        document.getElementById('nombre').value = campo.nombre;
        
        document.getElementById('obligatorio').checked = campo.obligatorio;
        document.getElementById('visible').checked = campo.visible;
        document.getElementById('editable').checked = campo.editable;
        document.getElementById('importable').checked = campo.importable;
        document.getElementById('exportable').checked = campo.exportable;
        document.getElementById('exportar_por_defecto').checked = campo.exportar_por_defecto;
        document.getElementById('activo').checked = campo.activo;
        
        if(campo.opciones && campo.opciones.length > 0) {
            const opcionesStr = campo.opciones.map(o => o.valor).join(', ');
            document.getElementById('opciones').value = opcionesStr;
        } else {
            document.getElementById('opciones').value = '';
        }
        
        toggleOpciones();
        new bootstrap.Modal(document.getElementById('modalCampo')).show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Sortable
        const el = document.getElementById('sortable-list');
        if (el) {
            new Sortable(el, {
                handle: '.handle',
                animation: 150,
                onEnd: function (evt) {
                    let order = [];
                    document.querySelectorAll('#sortable-list tr').forEach((row, index) => {
                        order.push({
                            id: row.getAttribute('data-id'),
                            orden: index
                        });
                    });
                    
                    fetch("{{ route('campos-personalizados.reorder') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ order: order })
                    }).then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            // Optional Toast for success
                        }
                    });
                }
            });
        }

        // SweetAlert para eliminar
        document.querySelectorAll('.form-eliminar').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Eliminar campo?',
                    text: "No se eliminarán los valores guardados en los equipos, pero el campo dejará de ser visible y gestionable.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
