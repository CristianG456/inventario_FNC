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
            <div class="col-md-4">
                <label class="form-label fw-medium small mb-1">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           class="form-control" placeholder="Serial, nombre, marca, activo fijo, usuario...">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium small mb-1">Activo Fijo</label>
                <input type="text" name="activo_fijo" value="{{ request('activo_fijo') }}"
                       class="form-control" placeholder="Filtrar por activo fijo...">
            </div>
            <div class="col-md-2">
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
                        <th>Serial Interno / Placa</th>
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
                                @if($equipo->activo_fijo)
                                    <span class="badge bg-secondary bg-opacity-50 font-monospace">
                                        {{ $equipo->activo_fijo }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
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
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    {{-- Acciones siempre disponibles --}}
                                    <a href="{{ route('equipos.show', $equipo) }}"
                                       class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('equipos.edit', $equipo) }}"
                                       class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Botones dinámicos según estado de asignación --}}
                                    @if(!$equipo->usuarioAsignado)
                                        {{-- Sin asignar: mostrar botón Asignar solo si está activo --}}
                                        @if($equipo->estado_operativo === 'activo')
                                        <button type="button"
                                                class="btn btn-sm btn-success"
                                                title="Asignar equipo"
                                                onclick="abrirModalAsignacion({{ $equipo->id }}, '{{ addslashes($equipo->nombre_equipo) }}', 'asignacion')">
                                            <i class="bi bi-person-plus me-1"></i>Asignar Funcionario
                                        </button>
                                        @else
                                        <button type="button"
                                                class="btn btn-sm btn-outline-success"
                                                title="Restaurar a Activo"
                                                onclick="abrirModalSimple({{ $equipo->id }}, '{{ addslashes($equipo->nombre_equipo) }}', 'restauracion')">
                                            <i class="bi bi-arrow-repeat"></i> Restaurar
                                        </button>
                                        @endif
                                    @else
                                        {{-- Ya asignado: opciones de gestión y ACTA --}}
                                        @if($equipo->estado_operativo === 'activo')
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Reemplazar usuario"
                                                onclick="abrirModalAsignacion({{ $equipo->id }}, '{{ addslashes($equipo->nombre_equipo) }}', 'reemplazo')">
                                            <i class="bi bi-arrow-left-right"></i> Reasignar
                                        </button>
                                        
                                        <a href="{{ route('equipos.acta', $equipo->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Generar Acta de Entrega PDF">
                                            <i class="bi bi-file-earmark-pdf"></i> Generar Acta
                                        </a>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Pasar a mantenimiento"
                                                onclick="abrirModalSimple({{ $equipo->id }}, '{{ addslashes($equipo->nombre_equipo) }}', 'mantenimiento')">
                                            <i class="bi bi-tools"></i>
                                        </button>
                                        @else
                                        <button type="button"
                                                class="btn btn-sm btn-outline-success"
                                                title="Restaurar a Activo"
                                                onclick="abrirModalSimple({{ $equipo->id }}, '{{ addslashes($equipo->nombre_equipo) }}', 'restauracion')">
                                            <i class="bi bi-arrow-repeat"></i> Restaurar
                                        </button>
                                        @endif
                                        
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                title="Retirar asignación"
                                                onclick="abrirModalSimple({{ $equipo->id }}, '{{ addslashes($equipo->nombre_equipo) }}', 'retiro')">
                                            <i class="bi bi-person-dash"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Dar de baja"
                                                onclick="abrirModalSimple({{ $equipo->id }}, '{{ addslashes($equipo->nombre_equipo) }}', 'baja')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif

                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
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
                            <td colspan="8" class="text-center py-5 text-muted">
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

{{-- ═══ MODAL: Acción con datos de usuario (asignar / reemplazar) ══════════ --}}
<div class="modal fade" id="modalAsignacion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formAsignacion" method="POST" action="{{ route('asignaciones.store') }}">
                @csrf
                <input type="hidden" name="equipo_id" id="asig_equipo_id">
                <input type="hidden" name="tipo_accion" id="asig_tipo_accion">

                <div class="modal-header" style="background: var(--sidebar-bg); color: #fff;">
                    <h5 class="modal-title" id="modalAsignacionTitulo">Asignar Equipo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-laptop me-1"></i>
                        Equipo: <strong id="asig_nombre_equipo"></strong>
                    </p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Cédula <span class="text-danger">*</span></label>
                            <input type="text" name="cedula" class="form-control" data-no-capitalize-first="true" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Cargo</label>
                            <input type="text" name="cargo" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Área</label>
                            <input type="text" name="area" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Dependencia</label>
                            <input type="text" name="dependencia" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Distrito</label>
                            <input type="text" name="distrito" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Seccional</label>
                            <input type="text" name="seccional" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Departamento</label>
                            <input type="text" name="departamento" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Empresa Propietaria</label>
                            <input type="text" name="empresa_propietaria" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Shortname</label>
                            <input type="text" name="shortname" class="form-control" data-no-capitalize-first="true">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Entregado Por</label>
                            <input type="text" name="entregado_por" class="form-control"
                                   value="{{ auth()->user()->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Fecha de Asignación</label>
                            <input type="date" name="fecha_accion" class="form-control"
                                   value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Motivo / Observaciones</label>
                            <textarea name="motivo" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnConfirmarAsig">
                        <i class="bi bi-check-lg me-1"></i>Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ MODAL: Acción simple (retiro, baja, mantenimiento) ════════════════ --}}
<div class="modal fade" id="modalSimple" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formSimple" method="POST" action="{{ route('asignaciones.store') }}">
                @csrf
                <input type="hidden" name="equipo_id" id="simple_equipo_id">
                <input type="hidden" name="tipo_accion" id="simple_tipo_accion">

                <div class="modal-header" style="background: var(--sidebar-bg); color: #fff;">
                    <h5 class="modal-title" id="modalSimpleTitulo">Acción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-laptop me-1"></i>
                        Equipo: <strong id="simple_nombre_equipo"></strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Motivo <span class="text-danger">*</span></label>
                        <textarea name="motivo" rows="3" class="form-control" required
                                  placeholder="Describa el motivo de esta acción..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Observaciones</label>
                        <textarea name="observaciones" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" id="btnConfirmarSimple">
                        <i class="bi bi-check-lg me-1"></i>Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const LABELS_ACCION = {
    asignacion:    'Asignar Equipo',
    reemplazo:     'Reemplazar Usuario',
    retiro:        'Retirar Asignación',
    mantenimiento: 'Pasar a Mantenimiento',
    restauracion:  'Restaurar Equipo',
    baja:          'Dar de Baja',
};

const COLORES_ACCION = {
    asignacion:    'btn-success',
    reemplazo:     'btn-primary',
    retiro:        'btn-secondary',
    mantenimiento: 'btn-warning',
    restauracion:  'btn-success',
    baja:          'btn-danger',
};

function abrirModalAsignacion(equipoId, nombreEquipo, tipo) {
    document.getElementById('asig_equipo_id').value   = equipoId;
    document.getElementById('asig_tipo_accion').value = tipo;
    document.getElementById('asig_nombre_equipo').textContent = nombreEquipo;
    document.getElementById('modalAsignacionTitulo').textContent = LABELS_ACCION[tipo] || tipo;

    const btn = document.getElementById('btnConfirmarAsig');
    btn.className = 'btn ' + (COLORES_ACCION[tipo] || 'btn-primary');
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Confirmar';

    // Limpiar formulario
    document.getElementById('formAsignacion').querySelectorAll('input:not([type=hidden]):not([type=date]), textarea').forEach(el => {
        if (el.name !== 'entregado_por') el.value = '';
    });

    new bootstrap.Modal(document.getElementById('modalAsignacion')).show();
}

function abrirModalSimple(equipoId, nombreEquipo, tipo) {
    document.getElementById('simple_equipo_id').value   = equipoId;
    document.getElementById('simple_tipo_accion').value = tipo;
    document.getElementById('simple_nombre_equipo').textContent = nombreEquipo;
    document.getElementById('modalSimpleTitulo').textContent = LABELS_ACCION[tipo] || tipo;

    const btn = document.getElementById('btnConfirmarSimple');
    btn.className = 'btn ' + (COLORES_ACCION[tipo] || 'btn-danger');

    document.getElementById('formSimple').querySelectorAll('textarea').forEach(el => el.value = '');

    new bootstrap.Modal(document.getElementById('modalSimple')).show();
}
</script>
@endpush
