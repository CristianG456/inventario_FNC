@extends('layouts.inventario')

@section('title', 'Detalle del Equipo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-info-circle me-2 text-info"></i>{{ $equipo->nombre_equipo }}
        <span class="badge bg-{{ $equipo->estado_badge }} ms-2 fs-6">{{ $equipo->estado_label }}</span>
    </h4>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('equipos.historial-vida', $equipo) }}" class="btn btn-outline-primary">
            <i class="bi bi-clock-history me-1"></i>Historial de Vida
        </a>
        <a href="{{ route('historial-tecnico.por-equipo', $equipo) }}" class="btn btn-outline-warning">
            <i class="bi bi-tools me-1"></i>Historial Técnico
        </a>
        <a href="{{ route('asignaciones.por-equipo', $equipo) }}" class="btn btn-outline-success">
            <i class="bi bi-person-fill-gear me-1"></i>Asignaciones
        </a>
        <a href="{{ route('equipos.edit', $equipo) }}" class="btn btn-warning text-white">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <a href="{{ route('equipos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Datos del equipo --}}
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-primary bg-opacity-10 fw-semibold border-0 py-3">
                <i class="bi bi-laptop me-2 text-primary"></i>Datos del Equipo
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Tipo</dt>
                    <dd class="col-sm-7">{{ $equipo->tipoRecurso?->nombre ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Serial</dt>
                    <dd class="col-sm-7 font-monospace">{{ $equipo->serial }}</dd>
                    <dt class="col-sm-5 text-muted">Activo Fijo</dt>
                    <dd class="col-sm-7 font-monospace fw-bold text-dark">{{ $equipo->activo_fijo ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Placa</dt>
                    <dd class="col-sm-7">{{ $equipo->placa ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Marca</dt>
                    <dd class="col-sm-7">{{ $equipo->marca }}</dd>
                    <dt class="col-sm-5 text-muted">Modelo</dt>
                    <dd class="col-sm-7">{{ $equipo->modelo }}</dd>
                    <dt class="col-sm-5 text-muted">Procesador</dt>
                    <dd class="col-sm-7">{{ $equipo->procesador ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">RAM</dt>
                    <dd class="col-sm-7">{{ $equipo->ram ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Disco</dt>
                    <dd class="col-sm-7">{{ $equipo->disco ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Sistema Op.</dt>
                    <dd class="col-sm-7">{{ $equipo->sistema_operativo ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Fecha Compra</dt>
                    <dd class="col-sm-7">{{ $equipo->fecha_compra?->format('d/m/Y') ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Fin Garantía</dt>
                    <dd class="col-sm-7">{{ $equipo->fin_garantia?->format('d/m/Y') ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Tiempo de Uso</dt>
                    <dd class="col-sm-7">{{ $equipo->tiempo_uso ?? '—' }}</dd>
                    @if($equipo->razon_estado)
                        <dt class="col-sm-5 text-muted">Razón Estado</dt>
                        <dd class="col-sm-7">{{ $equipo->razon_estado }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- Campos Personalizados --}}
    @if($equipo->camposPersonalizadosValores->isNotEmpty())
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-dark bg-opacity-10 fw-semibold border-0 py-3">
                <i class="bi bi-ui-checks-grid me-2 text-dark"></i>Información Adicional
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    @foreach($equipo->camposPersonalizadosValores as $cv)
                        @if($cv->campoPersonalizado && $cv->campoPersonalizado->visible)
                            <dt class="col-sm-5 text-muted">{{ $cv->campoPersonalizado->nombre }}</dt>
                            <dd class="col-sm-7">
                                @if($cv->campoPersonalizado->tipo === 'boolean')
                                    <span class="badge bg-{{ $cv->valor == '1' ? 'success' : 'secondary' }}">
                                        {{ $cv->valor == '1' ? 'Sí' : 'No' }}
                                    </span>
                                @elseif($cv->campoPersonalizado->tipo === 'url' && filter_var($cv->valor, FILTER_VALIDATE_URL))
                                    <a href="{{ $cv->valor }}" target="_blank" class="text-break"><i class="bi bi-link-45deg"></i> Ver Enlace</a>
                                @elseif($cv->campoPersonalizado->tipo === 'multiselect')
                                    @php
                                        $valores = is_string($cv->valor) ? json_decode($cv->valor, true) : $cv->valor;
                                    @endphp
                                    @if(is_array($valores))
                                        {{ implode(', ', $valores) }}
                                    @else
                                        {{ $cv->valor ?: '—' }}
                                    @endif
                                @else
                                    {{ $cv->valor ?: '—' }}
                                @endif
                            </dd>
                        @endif
                    @endforeach
                </dl>
            </div>
        </div>
    </div>
    @endif
    
    {{-- Usuario asignado --}}
    <div class="col-lg-6">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-success bg-opacity-10 fw-semibold border-0 py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person me-2 text-success"></i>Usuario Asignado</span>
                @if($equipo->usuarioAsignado)
                    <span class="badge bg-success">Asignado</span>
                @else
                    <span class="badge bg-secondary">Sin asignar</span>
                @endif
            </div>
            <div class="card-body">
                @if($equipo->usuarioAsignado)
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Nombre</dt>
                        <dd class="col-sm-7 fw-bold">{{ $equipo->usuarioAsignado->nombre }}</dd>
                        <dt class="col-sm-5 text-muted">Cédula</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->cedula }}</dd>
                        <dt class="col-sm-5 text-muted">Empresa Propietaria</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->empresa_propietaria ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Dependencia</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->dependencia ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Fuente de Recurso</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->fuente_recurso ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Empresa Funcionario</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->empresa_funcionario ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Emp. o Contratista</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->tipo_vinculacion ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Shortname</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->shortname ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Departamento</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->departamento ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Ciudad</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->ciudad ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Cargo</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->cargo ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Área</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->area ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Piso</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->piso ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Distrito</dt>
                        <dd class="col-sm-7 fw-bold">{{ $equipo->usuarioAsignado->distrito ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Seccional</dt>
                        <dd class="col-sm-7 fw-bold">{{ $equipo->usuarioAsignado->seccional ?? '—' }}</dd>
                    </dl>
                @else
                    <p class="text-muted mb-0">Sin usuario asignado.</p>
                @endif
            </div>
        </div>

        {{-- Responsable Temporal --}}
        @if($equipo->responsable_nombre)
        <div class="card mb-4 border-0 shadow-sm mt-3">
            <div class="card-header bg-secondary bg-opacity-10 fw-semibold border-0 py-3">
                <i class="bi bi-person-badge me-2 text-secondary"></i>Responsable Temporal
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Nombre</dt>
                    <dd class="col-sm-7 fw-bold">{{ $equipo->responsable_nombre }}</dd>
                    <dt class="col-sm-5 text-muted">Cédula</dt>
                    <dd class="col-sm-7">{{ $equipo->responsable_cedula ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Cargo</dt>
                    <dd class="col-sm-7">{{ $equipo->responsable_cargo ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Ciudad</dt>
                    <dd class="col-sm-7">{{ $equipo->responsable_ciudad ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Área</dt>
                    <dd class="col-sm-7">{{ $equipo->responsable_area ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Tipo Recurso</dt>
                    <dd class="col-sm-7">{{ $equipo->responsable_tipo_recurso ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Fechas</dt>
                    <dd class="col-sm-7">
                        {{ $equipo->fecha_inicio_responsable?->format('d/m/Y') ?? '—' }}
                        al
                        {{ $equipo->fecha_fin_responsable?->format('d/m/Y') ?? '—' }}
                    </dd>
                </dl>
            </div>
        </div>
        @endif

        {{-- Periféricos --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning bg-opacity-10 fw-semibold border-0 py-3">
                <i class="bi bi-usb-plug me-2 text-warning"></i>Periféricos
            </div>
            <div class="card-body">
                @if($equipo->periferico)
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Teléfono Fijo</dt>
                        <dd class="col-sm-7">{{ $equipo->periferico->telefono ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Teclado</dt>
                        <dd class="col-sm-7">{{ $equipo->periferico->teclado ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Mouse</dt>
                        <dd class="col-sm-7">{{ $equipo->periferico->mouse ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Cámara</dt>
                        <dd class="col-sm-7">{{ $equipo->periferico->camara ?? '—' }}</dd>
                    </dl>
                @else
                    <p class="text-muted mb-0">Sin periféricos registrados.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Últimas asignaciones --}}
    @if($equipo->asignaciones->isNotEmpty())
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success bg-opacity-10 fw-semibold border-0 py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person-fill-gear me-2 text-success"></i>Últimas Asignaciones</span>
                <a href="{{ route('asignaciones.por-equipo', $equipo) }}"
                   class="btn btn-sm btn-outline-success">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipo->asignaciones as $asig)
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $asig->tipo_accion_color }}">
                                        {{ $asig->tipo_accion_label }}
                                    </span>
                                </td>
                                <td class="small">{{ $asig->usuario_nombre ?? '—' }}</td>
                                <td class="small">{{ $asig->fecha_accion?->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Últimos eventos técnicos --}}
    @if($equipo->historialTecnico->isNotEmpty())
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning bg-opacity-10 fw-semibold border-0 py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-tools me-2 text-warning"></i>Últimos Eventos Técnicos</span>
                <a href="{{ route('historial-tecnico.por-equipo', $equipo) }}"
                   class="btn btn-sm btn-outline-warning">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipo->historialTecnico as $ht)
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $ht->tipo_evento_color }}">
                                        <i class="bi {{ $ht->tipo_evento_icono }}"></i>
                                    </span>
                                </td>
                                <td class="small">{{ Str::limit($ht->descripcion, 50) }}</td>
                                <td class="small">{{ $ht->fecha_evento?->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Licencias Asignadas --}}
    @if($equipo->licenciaAsignaciones && $equipo->licenciaAsignaciones->isNotEmpty())
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info bg-opacity-10 fw-semibold border-0 py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-key me-2 text-info"></i>Licencias Asignadas</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Licencia</th>
                                <th>Tipo</th>
                                <th>Fecha Asignación</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipo->licenciaAsignaciones as $la)
                            <tr>
                                <td>
                                    @if($la->licencia)
                                        <a href="{{ route('licencias.show', $la->licencia) }}" class="text-decoration-none fw-medium">
                                            {{ $la->licencia->nombre }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $la->licencia ? $la->licencia->tipo_licencia : 'N/A' }}</td>
                                <td class="small">{{ $la->fecha_asignacion?->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $la->estado === 'Activa' ? 'success' : 'secondary' }}">
                                        {{ $la->estado }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Checklists --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-secondary bg-opacity-10 fw-semibold border-0 py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard-check me-2 text-secondary"></i>Checklists Técnicos</span>
                <a href="{{ route('checklists.create') }}?equipo_id={{ $equipo->id }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nuevo Checklist
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Responsable TI</th>
                                <th>Orden de Trabajo</th>
                                <th>Resultado</th>
                                <th>FNC</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equipo->checklists as $cl)
                                <tr>
                                    <td>{{ $cl->responsable_ti ?? '—' }}</td>
                                    <td>{{ $cl->orden_trabajo ?? '—' }}</td>
                                    <td>{{ $cl->resultado ?? '—' }}</td>
                                    <td>{{ $cl->fnc ?? '—' }}</td>
                                    <td>{{ $cl->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('checklists.show', $cl) }}"
                                           class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('checklists.edit', $cl) }}"
                                           class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Sin checklists registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
