@extends('layouts.inventario')

@section('title', 'Equipos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-laptop me-2 text-primary"></i>Inventario de Equipos</h4>
    <div class="d-flex gap-2">
        @can('equipos.crear')
        <a href="{{ route('equipos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Equipo
        </a>
        @endcan
    </div>
</div>

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('equipos.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <label class="form-label fw-medium small mb-1">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           class="form-control" placeholder="Serial, nombre, marca, activo fijo, usuario...">
                </div>
            </div>
            <div class="col-12 col-md-2">
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
            <div class="col-12 col-md-2">
                <label class="form-label fw-medium small mb-1">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Asignado</option>
                    <option value="disponible" {{ request('estado') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="almacenado" {{ request('estado') === 'almacenado' ? 'selected' : '' }}>Almacenado</option>
                    <option value="mantenimiento" {{ request('estado') === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    <option value="baja" {{ request('estado') === 'baja' ? 'selected' : '' }}>Baja</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
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
                        <th>Responsable</th>
                        <th>Funcionario Asignado</th>
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
                                @if($equipo->placa || $equipo->activo_fijo)
                                    <span class="badge bg-secondary bg-opacity-50 font-monospace">
                                        {{ $equipo->placa ?? $equipo->activo_fijo }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $tipoName = strtolower($equipo->tipoRecurso?->nombre ?? '');
                                    $bgClass = 'bg-secondary bg-opacity-75';
                                    if (str_contains($tipoName, 'portatil') || str_contains($tipoName, 'laptop')) {
                                        $bgClass = 'bg-primary';
                                    } elseif (str_contains($tipoName, 'escritorio') || str_contains($tipoName, 'desktop')) {
                                        $bgClass = 'bg-success';
                                    } elseif (str_contains($tipoName, 'impresora') || str_contains($tipoName, 'escaner')) {
                                        $bgClass = 'bg-info text-dark';
                                    } elseif (str_contains($tipoName, 'microfono') || str_contains($tipoName, 'camara')) {
                                        $bgClass = 'bg-warning text-dark';
                                    } elseif (str_contains($tipoName, 'servidor') || str_contains($tipoName, 'switch') || str_contains($tipoName, 'router')) {
                                        $bgClass = 'bg-danger';
                                    } elseif (str_contains($tipoName, 'telefono') || str_contains($tipoName, 'movil') || str_contains($tipoName, 'tablet')) {
                                        $bgClass = 'bg-dark';
                                    } elseif ($tipoName) {
                                        $bgClass = 'bg-secondary';
                                    }
                                @endphp
                                <span class="badge {{ $bgClass }}">
                                    {{ $equipo->tipoRecurso?->nombre ?? '—' }}
                                </span>
                            </td>
                            <td>
                                {{ $equipo->marca }}
                                <br><small class="text-muted">{{ $equipo->modelo }}</small>
                            </td>
                            <td>
                                @php
                                    $responsableNombre = trim((string) ($equipo->responsable_nombre ?? ''));
                                    $responsableCedula = trim((string) ($equipo->responsable_cedula ?? ''));
                                @endphp
                                @if($responsableNombre !== '')
                                    <span class="fw-medium">{{ $responsableNombre }}</span>
                                @else
                                    <span class="text-muted fst-italic">Sin responsable</span>
                                @endif
                                @if($responsableCedula !== '')
                                    <br><small class="text-muted">CC: {{ $responsableCedula }}</small>
                                @endif
                            </td>
                            <td>
                                @if($equipo->usuarioAsignado)
                                    @php
                                        $nombreMostrar = trim((string) ($equipo->usuarioAsignado->nombre ?? ''));
                                        $cedulaAsignada = trim((string) ($equipo->usuarioAsignado->cedula ?? ''));
                                        $placeholders = ['SIN ASIGNAR', 'N/A', 'NA', 'NO APLICA', 'NULL', '-'];
                                        $nombreNormalizado = strtoupper($nombreMostrar);
                                        $tieneAsignacionReal =
                                            $nombreMostrar !== '' &&
                                            !in_array($nombreNormalizado, $placeholders, true);
                                    @endphp
                                    @if($tieneAsignacionReal)
                                        <span class="fw-medium">{{ $nombreMostrar }}</span>
                                        <br><small class="text-muted">CC: {{ $cedulaAsignada }}</small>
                                    @else
                                        <span class="text-muted fst-italic">Sin préstamo</span>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">Sin préstamo</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $nombreAsignado = trim((string) ($equipo->usuarioAsignado->nombre ?? ''));
                                    $placeholdersAsignacion = ['SIN ASIGNAR', 'N/A', 'NA', 'NO APLICA', 'NULL', '-'];
                                    $tieneFuncionarioReal =
                                        $nombreAsignado !== '' &&
                                        !in_array(strtoupper($nombreAsignado), $placeholdersAsignacion, true);

                                    $estadoMostrado = $equipo->estado_label;
                                    if ($equipo->estado_operativo === 'mantenimiento' && !$tieneFuncionarioReal) {
                                        $estadoMostrado = 'Disponible / Mantenimiento';
                                    }
                                @endphp
                                <span class="badge bg-{{ $equipo->estado_badge }}">
                                    {{ $estadoMostrado }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    {{-- Acciones siempre disponibles --}}
                                    <a href="{{ route('equipos.show', $equipo) }}"
                                       class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('equipos.editar')
                                    <a href="{{ route('equipos.edit', $equipo) }}"
                                       class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan

                                    {{-- Botones dinámicos según estado de préstamo --}}
                                    @can('equipos.crear')
                                    @if(!$equipo->usuarioAsignado)
                                        {{-- Sin préstamo: mostrar botón Registrar préstamo solo si está activo --}}
                                        @if(in_array($equipo->estado_operativo, ['activo', 'disponible'], true))
                                        <button type="button"
                                                class="btn btn-sm btn-success"
                                            title="Registrar préstamo"
                                                onclick="abrirModalAsignacion({{ $equipo->id }}, '{{ addslashes($equipo->nombre_equipo) }}', 'asignacion')">
                                            <i class="bi bi-person-plus"></i>
                                        </button>
                                        @else
                                        <button type="button"
                                                class="btn btn-sm btn-outline-success"
                                                title="Restaurar a Activo"
                                                onclick="abrirModalSimple({{ $equipo->id }}, '{{ addslashes($equipo->nombre_equipo) }}', 'restauracion')">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                        @endif
                                    @else
                                        {{-- Ya asignado: opciones de gestión y ACTA --}}
                                        @if(in_array($equipo->estado_operativo, ['activo', 'asignado'], true))
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Reemplazar préstamo"
                                                onclick="abrirModalAsignacion({{ $equipo->id }}, '{{ addslashes($equipo->nombre_equipo) }}', 'reemplazo')">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </button>
                                        
                                        <a href="{{ route('equipos.acta', $equipo->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Generar Acta de Entrega PDF">
                                            <i class="bi bi-file-earmark-pdf"></i>
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
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                        @endif
                                        
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                title="Retiro de funcionario"
                                                onclick="abrirModalSimple({{ $equipo->id }}, '{{ addslashes($equipo->nombre_equipo) }}', 'retiro')">
                                            <i class="bi bi-person-dash"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Retiro definitivo del activo"
                                                onclick="abrirModalSimple({{ $equipo->id }}, '{{ addslashes($equipo->nombre_equipo) }}', 'baja')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                    @endcan

                                    @can('equipos.eliminar')
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Eliminar"
                                            data-delete-url="{{ route('equipos.destroy', $equipo) }}"
                                            data-delete-name="{{ $equipo->nombre_equipo }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
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

{{-- ═══ MODAL: Acción con datos de usuario (préstamo / reemplazo) ══════════ --}}
<div class="modal fade" id="modalAsignacion" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <form id="formAsignacion" method="POST" action="{{ route('asignaciones.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="equipo_id" id="asig_equipo_id">
                <input type="hidden" name="tipo_accion" id="asig_tipo_accion">
                <input type="hidden" name="return_to" id="asig_return_to" value="{{ request()->fullUrl() }}">

                <div class="modal-header equipo-modal-header">
                    <h5 class="modal-title" id="modalAsignacionTitulo">Registrar Préstamo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-laptop me-1"></i>
                        Equipo: <strong id="asig_nombre_equipo"></strong>
                    </p>

                    <div class="row g-3">
                        <input type="hidden" name="nombre" id="asig_nombre_hidden" required>
                        <input type="hidden" name="cedula" id="asig_cedula_hidden" required>
                        <input type="hidden" name="cargo" id="asig_cargo_hidden">
                        <input type="hidden" name="area" id="asig_area_hidden">
                        <input type="hidden" name="dependencia" id="asig_dependencia_hidden">
                        <input type="hidden" name="distrito" id="asig_distrito_hidden">
                        <input type="hidden" name="seccional" id="asig_seccional_hidden">
                        <input type="hidden" name="ciudad" id="asig_ciudad_hidden">
                        <input type="hidden" name="departamento" id="asig_departamento_hidden">
                        <input type="hidden" name="empresa_propietaria" id="asig_empresa_propietaria_hidden">
                        <input type="hidden" name="empresa_funcionario" id="asig_empresa_funcionario_hidden">
                        <input type="hidden" name="tipo_vinculacion" id="asig_tipo_vinculacion_hidden">
                        <input type="hidden" name="shortname" id="asig_shortname_hidden">

                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Selecciona un funcionario elegible. Si tiene activos, debe tener autorizaciones cargadas en el módulo Funcionarios.
                            </small>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAbrirSelectorFuncionario">
                                <i class="bi bi-people me-1"></i>Seleccionar funcionario elegible
                            </button>
                        </div>

                        <div class="col-12">
                            <div class="border rounded p-3 bg-light" id="resumenFuncionarioSeleccionado">
                                <div class="text-muted">Aún no has seleccionado un funcionario.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Entregado Por</label>
                            <input type="text" name="entregado_por" class="form-control"
                                   value="{{ auth()->user()->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Fecha de Préstamo</label>
                            <input type="date" name="fecha_accion" class="form-control"
                                   value="{{ date('Y-m-d') }}">
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

{{-- ═══ MODAL: Selector de funcionarios elegibles ══════════════════════════ --}}
<div class="modal fade" id="modalSelectorFuncionario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-people me-2"></i>Funcionarios elegibles</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="filtroFuncionarioElegible" class="form-control" placeholder="Buscar por nombre, cédula, cargo o área...">
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Funcionario</th>
                                <th>Cédula</th>
                                <th>Cargo / Área</th>
                                <th>Estado de elegibilidad</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tablaFuncionariosElegibles">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Cargando funcionarios...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ MODAL: Acción simple (retiro, baja, mantenimiento) ════════════════ --}}
<div class="modal fade" id="modalSimple" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <form id="formSimple" method="POST" action="{{ route('asignaciones.store') }}">
                @csrf
                <input type="hidden" name="equipo_id" id="simple_equipo_id">
                <input type="hidden" name="tipo_accion" id="simple_tipo_accion">
                <input type="hidden" name="return_to" id="simple_return_to" value="{{ request()->fullUrl() }}">

                <div class="modal-header equipo-modal-header">
                    <h5 class="modal-title" id="modalSimpleTitulo">Acción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-laptop me-1"></i>
                        Equipo: <strong id="simple_nombre_equipo"></strong>
                    </p>
                    <div class="mb-3" id="simple_motivo_wrap">
                        <label class="form-label fw-medium" id="simple_motivo_label">Motivo <span class="text-danger">*</span></label>
                        <textarea name="motivo" id="simple_motivo" rows="3" class="form-control" required
                                  placeholder="Describa el motivo de esta acción..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium" id="simple_observaciones_label">Observaciones</label>
                        <textarea name="observaciones" id="simple_observaciones" rows="2" class="form-control"></textarea>
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
    asignacion:    'Registrar Préstamo',
    reemplazo:     'Reemplazar Usuario',
    retiro:        'Retiro de funcionario',
    mantenimiento: 'Pasar a Mantenimiento',
    restauracion:  'Restaurar Equipo',
    baja:          'Retiro definitivo del activo',
};

const COLORES_ACCION = {
    asignacion:    'btn-success',
    reemplazo:     'btn-primary',
    retiro:        'btn-secondary',
    mantenimiento: 'btn-warning',
    restauracion:  'btn-success',
    baja:          'btn-danger',
};

const FUNCIONARIOS_ELEGIBLES_URL = @json(route('asignaciones.funcionarios-elegibles'));
let funcionariosElegiblesCache = [];
let funcionariosBloqueadosCache = [];

function poblarFormularioFuncionario(funcionario) {
    if (!funcionario) return;

    const setHidden = (id, value) => {
        const input = document.getElementById(id);
        if (input) input.value = value ?? '';
    };

    setHidden('asig_nombre_hidden', funcionario.nombre);
    setHidden('asig_cedula_hidden', funcionario.identificacion);
    setHidden('asig_cargo_hidden', funcionario.cargo);
    setHidden('asig_area_hidden', funcionario.area);
    setHidden('asig_dependencia_hidden', funcionario.dependencia);
    setHidden('asig_distrito_hidden', funcionario.distrito);
    setHidden('asig_seccional_hidden', funcionario.seccional);
    setHidden('asig_ciudad_hidden', funcionario.ciudad);
    setHidden('asig_departamento_hidden', funcionario.departamento);
    setHidden('asig_empresa_propietaria_hidden', funcionario.empresa_propietaria);
    setHidden('asig_empresa_funcionario_hidden', funcionario.empresa_funcionario);
    setHidden('asig_tipo_vinculacion_hidden', funcionario.tipo_vinculacion);
    setHidden('asig_shortname_hidden', funcionario.shortname);

    const resumen = document.getElementById('resumenFuncionarioSeleccionado');
    if (resumen) {
        const estado = funcionario.activos_count === 0
            ? 'Sin activos'
            : `${funcionario.autorizaciones_count} acta(s) disponible(s)`;

        resumen.innerHTML = `
            <div class="fw-semibold">${funcionario.nombre || '—'}</div>
            <div class="small text-muted">CC: ${funcionario.identificacion || '—'}</div>
            <div class="small text-muted">${funcionario.cargo || '—'} / ${funcionario.area || '—'}</div>
            <div class="small mt-1"><span class="badge bg-info text-dark">${estado}</span></div>
        `;
    }
}

function renderFuncionariosElegibles(lista) {
    const tbody = document.getElementById('tablaFuncionariosElegibles');
    if (!tbody) return;

    if (!Array.isArray(lista) || lista.length === 0) {
        const filtro = (document.getElementById('filtroFuncionarioElegible')?.value || '').trim();
        if (filtro !== '' && funcionariosBloqueadosCache.length > 0) {
            const sugerencias = funcionariosBloqueadosCache.slice(0, 3).map((f) => {
                return `${f.nombre} (CC ${f.identificacion}): faltan ${f.autorizaciones_faltantes} autorizacion(es).`;
            }).join('<br>');

            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-warning py-4">No hay elegibles con ese criterio.<br><small>${sugerencias}</small></td></tr>`;
            return;
        }

        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No se encontraron funcionarios elegibles.</td></tr>';
        return;
    }

    tbody.innerHTML = lista.map((f) => {
        const estado = f.activos_count === 0
            ? '<span class="badge bg-success">Sin activos</span>'
            : `<span class="badge bg-info text-dark">${f.autorizaciones_count} acta(s) disponible(s)</span>`;

        const cargoArea = [f.cargo, f.area].filter(Boolean).join(' / ') || '—';

        return `
            <tr>
                <td>${f.nombre || '—'}</td>
                <td>${f.identificacion || '—'}</td>
                <td>${cargoArea}</td>
                <td>${estado}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-primary" data-funcionario-id="${f.id}">
                        Seleccionar
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function abrirModalAsignacion(equipoId, nombreEquipo, tipo) {
    document.getElementById('asig_equipo_id').value   = equipoId;
    document.getElementById('asig_tipo_accion').value = tipo;
    document.getElementById('asig_return_to').value   = window.location.href;
    document.getElementById('asig_nombre_equipo').textContent = nombreEquipo;
    document.getElementById('modalAsignacionTitulo').textContent = LABELS_ACCION[tipo] || tipo;

    const btn = document.getElementById('btnConfirmarAsig');
    btn.className = 'btn ' + (COLORES_ACCION[tipo] || 'btn-primary');
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Confirmar';

    // Limpiar formulario
    document.getElementById('formAsignacion').querySelectorAll('input:not([type=hidden]):not([type=date]), textarea').forEach(el => {
        if (el.name !== 'entregado_por') el.value = '';
    });

    ['asig_nombre_hidden','asig_cedula_hidden','asig_cargo_hidden','asig_area_hidden','asig_dependencia_hidden','asig_distrito_hidden','asig_seccional_hidden','asig_ciudad_hidden','asig_departamento_hidden','asig_empresa_propietaria_hidden','asig_empresa_funcionario_hidden','asig_tipo_vinculacion_hidden','asig_shortname_hidden']
        .forEach((id) => {
            const input = document.getElementById(id);
            if (input) input.value = '';
        });

    const resumen = document.getElementById('resumenFuncionarioSeleccionado');
    if (resumen) {
        resumen.innerHTML = '<div class="text-muted">Aún no has seleccionado un funcionario.</div>';
    }

    new bootstrap.Modal(document.getElementById('modalAsignacion')).show();
}

function abrirModalSimple(equipoId, nombreEquipo, tipo) {
    document.getElementById('simple_equipo_id').value   = equipoId;
    document.getElementById('simple_tipo_accion').value = tipo;
    document.getElementById('simple_return_to').value   = window.location.href;
    document.getElementById('simple_nombre_equipo').textContent = nombreEquipo;
    document.getElementById('modalSimpleTitulo').textContent = LABELS_ACCION[tipo] || tipo;

    const btn = document.getElementById('btnConfirmarSimple');
    btn.className = 'btn ' + (COLORES_ACCION[tipo] || 'btn-danger');

    const motivoWrap = document.getElementById('simple_motivo_wrap');
    const motivoInput = document.getElementById('simple_motivo');
    const observacionesInput = document.getElementById('simple_observaciones');
    const observacionesLabel = document.getElementById('simple_observaciones_label');

    if (['retiro', 'baja', 'mantenimiento', 'restauracion'].includes(tipo)) {
        motivoWrap.classList.add('d-none');
        motivoInput.required = false;
        motivoInput.value = '';
        observacionesInput.required = true;
        observacionesInput.placeholder = tipo === 'baja'
            ? 'Describa la observacion del retiro definitivo del activo...'
            : (tipo === 'mantenimiento'
                ? 'Describa la observacion del envío a mantenimiento...'
            : (tipo === 'retiro'
                ? 'Describa la observacion del retiro de funcionario...'
                : 'Describa la observacion de la restauracion...'));
        observacionesLabel.innerHTML = 'Observaciones <span class="text-danger">*</span>';
    } else {
        motivoWrap.classList.remove('d-none');
        motivoInput.required = true;
        observacionesInput.required = false;
        observacionesInput.placeholder = '';
        observacionesLabel.textContent = 'Observaciones';
    }

    document.getElementById('formSimple').querySelectorAll('textarea').forEach(el => el.value = '');

    new bootstrap.Modal(document.getElementById('modalSimple')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    const modalSelectorFuncionarioEl = document.getElementById('modalSelectorFuncionario');
    const modalSelectorFuncionario = modalSelectorFuncionarioEl ? new bootstrap.Modal(modalSelectorFuncionarioEl) : null;
    const btnAbrirSelectorFuncionario = document.getElementById('btnAbrirSelectorFuncionario');
    const filtroFuncionarioElegible = document.getElementById('filtroFuncionarioElegible');

    const cargarFuncionariosElegibles = (termino = '') => {
        const tbody = document.getElementById('tablaFuncionariosElegibles');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Cargando funcionarios...</td></tr>';
        }

        const url = new URL(FUNCIONARIOS_ELEGIBLES_URL, window.location.origin);
        if (termino.trim() !== '') {
            url.searchParams.set('q', termino.trim());
        }

        fetch(url.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => response.json())
            .then((payload) => {
                funcionariosElegiblesCache = Array.isArray(payload.data) ? payload.data : [];
                funcionariosBloqueadosCache = Array.isArray(payload.bloqueados) ? payload.bloqueados : [];
                renderFuncionariosElegibles(funcionariosElegiblesCache);
            })
            .catch(() => {
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">No se pudo cargar la lista de funcionarios elegibles.</td></tr>';
                }
            });
    };

    if (btnAbrirSelectorFuncionario && modalSelectorFuncionario) {
        btnAbrirSelectorFuncionario.addEventListener('click', function() {
            if (filtroFuncionarioElegible) {
                filtroFuncionarioElegible.value = '';
            }
            modalSelectorFuncionario.show();
            cargarFuncionariosElegibles('');
        });
    }

    if (filtroFuncionarioElegible) {
        filtroFuncionarioElegible.addEventListener('input', function() {
            const termino = this.value.trim().toLowerCase();
            if (termino === '') {
                renderFuncionariosElegibles(funcionariosElegiblesCache);
                return;
            }

            const filtrados = funcionariosElegiblesCache.filter((f) => {
                const texto = [
                    f.nombre,
                    f.identificacion,
                    f.cargo,
                    f.area,
                    f.departamento,
                    f.ciudad,
                ].join(' ').toLowerCase();

                return texto.includes(termino);
            });

            renderFuncionariosElegibles(filtrados);
        });
    }

    const tablaFuncionariosElegibles = document.getElementById('tablaFuncionariosElegibles');
    if (tablaFuncionariosElegibles && modalSelectorFuncionario) {
        tablaFuncionariosElegibles.addEventListener('click', function(e) {
            const button = e.target.closest('button[data-funcionario-id]');
            if (!button) return;

            const funcionarioId = Number(button.getAttribute('data-funcionario-id'));
            const funcionario = funcionariosElegiblesCache.find((f) => Number(f.id) === funcionarioId) || null;
            if (!funcionario) return;

            poblarFormularioFuncionario(funcionario);
            modalSelectorFuncionario.hide();
        });
    }

    const formAsignacion = document.getElementById('formAsignacion');
    if(formAsignacion) {
        formAsignacion.addEventListener('submit', function(e) {
            const tipoAccion = document.getElementById('asig_tipo_accion').value;
            const cedulaSeleccionada = (document.getElementById('asig_cedula_hidden')?.value || '').trim();

            if (['asignacion', 'reemplazo'].includes(tipoAccion) && cedulaSeleccionada === '') {
                e.preventDefault();
                Swal.fire('Funcionario requerido', 'Debes seleccionar un funcionario elegible antes de confirmar.', 'warning');
                return;
            }

            if (tipoAccion === 'reemplazo') {
                e.preventDefault();
                Swal.fire({
                    title: 'Devolución requerida',
                    text: "Este activo actualmente se encuentra asignado. ¿Ya fue recibida y validada el Acta de Devolución?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, registrar devolución',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const equipoId = document.getElementById('asig_equipo_id').value;
                        const token = document.querySelector('input[name="_token"]').value;

                        // Ejecutar silenciosamente el retiro
                        fetch("{{ route('asignaciones.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                equipo_id: equipoId,
                                tipo_accion: 'retiro',
                                motivo: 'Acta de devolución validada',
                                observaciones: 'Generado automáticamente antes de reasignar',
                                fecha_accion: new Date().toISOString().split('T')[0]
                            })
                        }).then(response => {
                            if(response.ok) {
                                // Cambiar el tipo a asignacion para el nuevo flujo
                                document.getElementById('asig_tipo_accion').value = 'asignacion';
                                formAsignacion.submit();
                            } else {
                                Swal.fire('Error', 'No se pudo registrar la devolución.', 'error');
                            }
                        }).catch(error => {
                            Swal.fire('Error', 'Ocurrió un error en la solicitud.', 'error');
                        });
                    } else {
                        Swal.fire('Operación cancelada', 'Debe validar la devolución antes de asignar a un nuevo funcionario.', 'info');
                    }
                });
            }
        });
    }

    const formSimple = document.getElementById('formSimple');
    if(formSimple) {
        formSimple.addEventListener('submit', function(e) {
            const tipoAccion = document.getElementById('simple_tipo_accion').value;
            if (tipoAccion === 'retiro') {
                e.preventDefault();
                Swal.fire({
                    title: 'Devolución requerida',
                    text: "¿Ya fue recibida y validada el Acta de Devolución?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, la entregó',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Cambiamos a submit nativo para evitar ciclo infinito
                        formSimple.submit();
                    } else {
                        Swal.fire('Operación cancelada', 'Debe validar el Acta de Devolución antes de registrar el retiro.', 'info');
                    }
                });
            }
        });
    }

});
</script>
@endpush
