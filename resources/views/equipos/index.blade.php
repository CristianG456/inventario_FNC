@extends('layouts.inventario')

@section('title', 'Equipos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-laptop me-2 text-primary"></i>Inventario de Equipos</h4>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalExportacion">
            <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
        </button>
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
                                @if($equipo->usuarioAsignado)
                                    @php
                                        $nombreMostrar = $equipo->usuarioAsignado->nombre;
                                        $cedulaAsignada = $equipo->usuarioAsignado->cedula;
                                        if ($nombreMostrar === 'Sin Asignar' && $cedulaAsignada && $cedulaAsignada !== 'Sin Asignar') {
                                            $func = \App\Models\Funcionario::where('identificacion', $cedulaAsignada)->first();
                                            if ($func && $func->nombres !== 'Sin Nombre Registrado') {
                                                $nombreMostrar = trim($func->nombres . ' ' . $func->apellidos);
                                            }
                                        }
                                    @endphp
                                    @if($nombreMostrar !== 'Sin Asignar')
                                        <span class="fw-medium">{{ $nombreMostrar }}</span>
                                    @else
                                        <span class="text-muted fst-italic">Sin nombre registrado</span>
                                    @endif
                                    <br><small class="text-muted">CC: {{ $cedulaAsignada }}</small>
                                @else
                                    <span class="text-muted fst-italic">Sin asignar</span>
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
                                                title="Asignar Funcionario"
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
                                        @if($equipo->estado_operativo === 'activo')
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Reasignar Funcionario"
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

                <div class="modal-header equipo-modal-header">
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

                <div class="modal-header equipo-modal-header">
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

{{-- ═══ MODAL: Exportación Inteligente ════════════════ --}}
<div class="modal fade" id="modalExportacion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formExportacion" method="GET" action="{{ route('equipos.exportar') }}">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-excel me-2"></i>Exportación Avanzada</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Selecciona las columnas que deseas incluir en el archivo Excel. Puedes guardar esta selección como plantilla para futuras exportaciones.
                    </p>
                    
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-primary">Plantillas Guardadas</label>
                            <select class="form-select form-select-sm" id="plantillaExportacionSelect">
                                <option value="">-- Seleccionar Plantilla (Opcional) --</option>
                                @if(isset($plantillasExportacion))
                                    @foreach($plantillasExportacion as $plantilla)
                                        <option value="{{ $plantilla->id }}" data-estandar="{{ json_encode($plantilla->columnas_estandar) }}" data-personalizadas="{{ json_encode($plantilla->columnas_personalizadas) }}">
                                            {{ $plantilla->nombre }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="card mb-3 border-secondary border-opacity-25">
                        <div class="card-header bg-light fw-bold py-2">
                            <i class="bi bi-list-check me-1"></i>Columnas Estándar del Sistema
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                @php
                                    $columnasEstandar = [
                                        'id' => 'ID Interno',
                                        'nombre_equipo' => 'Nombre del Equipo',
                                        'serial' => 'Serial',
                                        'activo_fijo' => 'Activo Fijo',
                                        'placa' => 'Placa / Inventario',
                                        'marca' => 'Marca',
                                        'modelo' => 'Modelo',
                                        'tipo' => 'Tipo de Equipo',
                                        'estado' => 'Estado Operativo',
                                        'usuario_asignado' => 'Usuario Asignado (Nombre)',
                                        'cedula_asignado' => 'Usuario Asignado (Cédula)'
                                    ];
                                @endphp
                                @foreach($columnasEstandar as $key => $label)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="columnas_estandar[]" value="{{ $key }}" id="col_est_{{ $key }}" checked>
                                        <label class="form-check-label small" for="col_est_{{ $key }}">{{ $label }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card mb-2 border-secondary border-opacity-25">
                        <div class="card-header bg-light fw-bold py-2">
                            <i class="bi bi-ui-checks-grid me-1"></i>Campos Personalizados (Dinámicos)
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2" id="contenedorCamposExportables">
                                @php
                                    $camposExportables = \App\Models\CampoPersonalizado::where('modulo', 'equipos')->where('exportable', true)->orderBy('orden')->get();
                                @endphp
                                @if($camposExportables->isEmpty())
                                    <div class="col-12 text-muted small fst-italic">No hay campos personalizados configurados como exportables.</div>
                                @else
                                    @foreach($camposExportables as $campo)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="columnas_personalizadas[]" value="{{ $campo->id }}" id="col_pers_{{ $campo->id }}" {{ $campo->exportar_por_defecto ? 'checked' : '' }}>
                                            <label class="form-check-label small text-primary" for="col_pers_{{ $campo->id }}">{{ $campo->nombre }}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-top">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="guardar_plantilla" name="guardar_plantilla" value="1">
                            <label class="form-check-label fw-bold text-success" for="guardar_plantilla">Guardar esta selección como nueva plantilla</label>
                        </div>
                        <div class="mt-2" id="div_nombre_plantilla" style="display: none;">
                            <input type="text" class="form-control form-control-sm" name="nombre_plantilla" placeholder="Ej: Reporte Mensual Gerencia">
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-download me-1"></i>Generar y Descargar Excel
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

document.addEventListener('DOMContentLoaded', function() {
    const formAsignacion = document.getElementById('formAsignacion');
    if(formAsignacion) {
        formAsignacion.addEventListener('submit', function(e) {
            const tipoAccion = document.getElementById('asig_tipo_accion').value;
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

    // Lógica para exportación: mostrar campo de nombre si se selecciona guardar
    const checkGuardarPlantilla = document.getElementById('guardar_plantilla');
    if (checkGuardarPlantilla) {
        checkGuardarPlantilla.addEventListener('change', function() {
            document.getElementById('div_nombre_plantilla').style.display = this.checked ? 'block' : 'none';
            if(this.checked) {
                document.querySelector('input[name="nombre_plantilla"]').setAttribute('required', 'required');
            } else {
                document.querySelector('input[name="nombre_plantilla"]').removeAttribute('required');
            }
        });
    }

    // Lógica para cargar plantilla guardada
    const selectPlantilla = document.getElementById('plantillaExportacionSelect');
    if(selectPlantilla) {
        selectPlantilla.addEventListener('change', function() {
            if(!this.value) return; // Si selecciona vacio, no hace nada (deja como está)
            
            const estandar = JSON.parse(this.options[this.selectedIndex].getAttribute('data-estandar') || '[]');
            const personalizadas = JSON.parse(this.options[this.selectedIndex].getAttribute('data-personalizadas') || '[]');
            
            // Desmarcar todo primero
            document.querySelectorAll('input[name="columnas_estandar[]"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('input[name="columnas_personalizadas[]"]').forEach(cb => cb.checked = false);
            
            // Marcar las estándar guardadas
            estandar.forEach(val => {
                const cb = document.getElementById('col_est_' + val);
                if(cb) cb.checked = true;
            });
            
            // Marcar las personalizadas guardadas
            personalizadas.forEach(val => {
                const cb = document.getElementById('col_pers_' + val);
                if(cb) cb.checked = true;
            });
        });
    }

    // Copiar filtros actuales de búsqueda al formulario de exportación
    const formExportacion = document.getElementById('formExportacion');
    if(formExportacion) {
        formExportacion.addEventListener('submit', function(e) {
            // Añadir campos ocultos con los valores de búsqueda actuales
            const searchParams = new URLSearchParams(window.location.search);
            
            // Eliminar inputs ocultos previos si existen
            formExportacion.querySelectorAll('.dynamic-filter').forEach(el => el.remove());

            for(const [key, value] of searchParams.entries()) {
                if(value && key !== 'page') {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    input.className = 'dynamic-filter';
                    formExportacion.appendChild(input);
                }
            }
            
            // Cerrar modal después de un breve retraso para permitir la descarga
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('modalExportacion')).hide();
            }, 1000);
        });
    }
});
</script>
@endpush
