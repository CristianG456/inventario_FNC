@extends('layouts.inventario')

@section('title', 'Detalle de Licencia')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-key text-primary me-2"></i>{{ $licencia->nombre }}
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('licencias.edit', $licencia) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <a href="{{ route('licencias.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-light fw-bold">Información de la Licencia</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Tipo:</dt>
                    <dd class="col-sm-7">{{ $licencia->tipo_licencia }}</dd>

                    <dt class="col-sm-5 text-muted">Estado:</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-{{ $licencia->estado === 'Activa' ? 'success' : ($licencia->estado === 'Suspendida' ? 'warning' : 'danger') }}">
                            {{ $licencia->estado }}
                        </span>
                    </dd>

                    <dt class="col-sm-5 text-muted">Cupos:</dt>
                    <dd class="col-sm-7">
                        <span class="badge {{ $licencia->cupos_disponibles > 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $licencia->cupos_asignados }} / {{ $licencia->cantidad_maxima }} Asignados
                        </span>
                    </dd>

                    <dt class="col-sm-5 text-muted">Inicio:</dt>
                    <dd class="col-sm-7">{{ $licencia->fecha_inicio ? $licencia->fecha_inicio->format('d/m/Y') : 'N/A' }}</dd>

                    <dt class="col-sm-5 text-muted">Vencimiento:</dt>
                    <dd class="col-sm-7 {{ $licencia->fecha_vencimiento && $licencia->fecha_vencimiento->isPast() ? 'text-danger fw-bold' : '' }}">
                        {{ $licencia->fecha_vencimiento ? $licencia->fecha_vencimiento->format('d/m/Y') : 'N/A' }}
                    </dd>
                </dl>
                @if($licencia->descripcion)
                <hr>
                <div class="text-muted small"><strong>Descripción:</strong><br>{{ $licencia->descripcion }}</div>
                @endif
                @if($licencia->observaciones)
                <hr>
                <div class="text-muted small"><strong>Observaciones:</strong><br>{{ $licencia->observaciones }}</div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <ul class="nav nav-tabs" id="licenciaTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="asignaciones-tab" data-bs-toggle="tab" data-bs-target="#asignaciones" type="button" role="tab">
                    <i class="bi bi-people me-1"></i>Asignaciones
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial" type="button" role="tab">
                    <i class="bi bi-clock-history me-1"></i>Historial
                </button>
            </li>
        </ul>
        <div class="tab-content border-start border-end border-bottom bg-white p-3 mb-4" id="licenciaTabsContent">
            <!-- Pestaña Asignaciones -->
            <div class="tab-pane fade show active" id="asignaciones" role="tabpanel">
                @if($licencia->asignaciones->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Funcionario</th>
                                <th>Equipo</th>
                                <th>Fecha Asig.</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($licencia->asignaciones as $asignacion)
                            <tr>
                                <td>{{ $asignacion->funcionario ? $asignacion->funcionario->nombre_completo : 'N/A' }}</td>
                                <td>
                                    @if($asignacion->equipo)
                                        <a href="{{ route('equipos.show', $asignacion->equipo) }}" class="fw-medium text-decoration-none">{{ $asignacion->equipo->placa ?? $asignacion->equipo->nombre_equipo ?? 'Ver Equipo' }}</a><br>
                                        <span class="text-muted small">Serial: {{ $asignacion->equipo->serial ?? 'N/A' }}</span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $asignacion->fecha_asignacion ? $asignacion->fecha_asignacion->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $asignacion->estado === 'Activa' ? 'success' : 'secondary' }}">
                                        {{ $asignacion->estado }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted mb-0">No hay asignaciones para esta licencia.</p>
                @endif
            </div>

            <!-- Pestaña Historial -->
            <div class="tab-pane fade" id="historial" role="tabpanel">
                @if($historial->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historial as $log)
                            <tr>
                                <td>{{ $log->fecha->format('d/m/Y H:i') }}</td>
                                <td>{{ $log->usuario ? $log->usuario->name : 'Sistema' }}</td>
                                <td>{{ $log->accion }}</td>
                                <td>{{ $log->observacion }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted mb-0">No hay historial registrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
