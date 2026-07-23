@extends('layouts.inventario')

@section('title', 'Detalle de Funcionario')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Detalle de Funcionario</h4>
        <small class="text-muted">Información general y activos asignados</small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('funcionarios.edit', $funcionario) }}" class="btn btn-warning text-white">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <a href="{{ route('funcionarios.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary bg-opacity-10 fw-semibold border-0 py-3">
                <i class="bi bi-person-vcard me-2 text-primary"></i>Datos del Funcionario
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Nombre</dt>
                    <dd class="col-sm-7 fw-semibold">{{ $funcionario->nombre_completo }}</dd>

                    <dt class="col-sm-5 text-muted">Identificación</dt>
                    <dd class="col-sm-7">{{ $funcionario->identificacion }}</dd>

                    <dt class="col-sm-5 text-muted">Cargo</dt>
                    <dd class="col-sm-7">{{ $funcionario->cargo ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Área</dt>
                    <dd class="col-sm-7">{{ $funcionario->area ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Departamento</dt>
                    <dd class="col-sm-7">{{ $funcionario->departamento ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Ciudad</dt>
                    <dd class="col-sm-7">{{ $funcionario->ciudad ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Estado</dt>
                    <dd class="col-sm-7">
                        <span class="badge {{ $funcionario->estado === 'Activo' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $funcionario->estado }}
                        </span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-info bg-opacity-10 fw-semibold border-0 py-3">
                <i class="bi bi-bar-chart me-2 text-info"></i>Resumen
            </div>
            <div class="card-body">
                <div class="d-flex gap-3 flex-wrap mb-3">
                    <span class="badge bg-primary fs-6">Activos: {{ $funcionario->equipos_asignados_count }}</span>
                    <span class="badge bg-success fs-6">Autorizaciones totales: {{ $funcionario->autorizaciones_activos_count }}</span>
                </div>
                <small class="text-muted">Cada activo adicional consume una autorización disponible (estado cargada).</small>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success bg-opacity-10 fw-semibold border-0 py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-check me-2 text-success"></i>Actas de autorización</span>
                <span class="badge bg-light text-dark border">Disponibles: {{ $funcionario->autorizacionesActivos->where('estado', 'cargada')->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Archivo</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Uso</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($funcionario->autorizacionesActivos as $acta)
                                <tr>
                                    <td>{{ $acta->id }}</td>
                                    <td class="small">{{ basename($acta->archivo) }}</td>
                                    <td>
                                        @if($acta->estado === 'cargada')
                                            <span class="badge bg-success">Cargada</span>
                                        @elseif($acta->estado === 'consumida')
                                            <span class="badge bg-primary">Consumida</span>
                                        @else
                                            <span class="badge bg-secondary">Anulada</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ optional($acta->created_at)->format('d/m/Y H:i') }}</td>
                                    <td class="small">
                                        @if($acta->estado === 'consumida' && $acta->asignacion_id)
                                            Asignación #{{ $acta->asignacion_id }}
                                        @elseif($acta->estado === 'anulada')
                                            {{ $acta->motivo_anulacion ?? 'Sin motivo' }}
                                        @else
                                            Pendiente de uso
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @if($acta->estado === 'cargada')
                                            <form method="POST" action="{{ route('funcionarios.autorizaciones.anular', [$funcionario, $acta]) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Anular</button>
                                            </form>
                                        @else
                                            <span class="text-muted small">No editable</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Sin actas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light fw-semibold border-0 py-3">
                <i class="bi bi-laptop me-2 text-secondary"></i>Activos Asignados
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Equipo</th>
                                <th>Serial</th>
                                <th>Estado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($funcionario->equiposAsignados as $asignado)
                                <tr>
                                    <td>{{ $asignado->equipo?->nombre_equipo ?? '—' }}</td>
                                    <td class="font-monospace">{{ $asignado->equipo?->serial ?? '—' }}</td>
                                    <td>{{ $asignado->equipo?->estado_label ?? '—' }}</td>
                                    <td class="text-end pe-3">
                                        @if($asignado->equipo)
                                            <a href="{{ route('equipos.show', $asignado->equipo) }}" class="btn btn-sm btn-outline-info" title="Ver equipo">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Sin activos asignados.</td>
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
