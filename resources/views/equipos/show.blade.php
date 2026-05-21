@extends('layouts.inventario')

@section('title', 'Detalle del Equipo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-info-circle me-2 text-info"></i>{{ $equipo->nombre_equipo }}
        <span class="badge bg-{{ $equipo->estado_badge }} ms-2 fs-6">{{ $equipo->estado_label }}</span>
    </h4>
    <div class="d-flex gap-2">
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
        <div class="card h-100">
            <div class="card-header bg-primary bg-opacity-10 fw-semibold border-0 py-3">
                <i class="bi bi-laptop me-2 text-primary"></i>Datos del Equipo
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Tipo</dt>
                    <dd class="col-sm-7">{{ $equipo->tipoRecurso?->nombre ?? '—' }}</dd>
                    <dt class="col-sm-5 text-muted">Serial</dt>
                    <dd class="col-sm-7 font-monospace">{{ $equipo->serial }}</dd>
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

    {{-- Usuario asignado --}}
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header bg-success bg-opacity-10 fw-semibold border-0 py-3">
                <i class="bi bi-person me-2 text-success"></i>Usuario Asignado
            </div>
            <div class="card-body">
                @if($equipo->usuarioAsignado)
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Empresa Propietaria</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->empresa_propietaria ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Dependencia</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->dependencia ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Fuente de Recurso</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->fuente_recurso ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Empresa Funcionario</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->empresa_funcionario ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Empleado o Contratista</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->tipo_vinculacion ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Shortname</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->shortname ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">Nombre</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->nombre }}</dd>
                        <dt class="col-sm-5 text-muted">Cédula</dt>
                        <dd class="col-sm-7">{{ $equipo->usuarioAsignado->cedula }}</dd>
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
                    </dl>
                @else
                    <p class="text-muted mb-0">Sin usuario asignado.</p>
                @endif
            </div>
        </div>

        {{-- Periféricos --}}
        <div class="card">
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

    {{-- Checklists --}}
    <div class="col-12">
        <div class="card">
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
