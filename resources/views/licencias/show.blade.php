@extends('layouts.inventario')

@section('title', 'Detalle de Licencia')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-key text-primary me-2"></i>{{ $licencia->nombre }}
    </h4>
    <div class="d-flex gap-2 flex-wrap">
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

                    @if($licencia->tipo_licencia === 'Suscripción')
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

                        <dt class="col-sm-5 text-muted">Renovación:</dt>
                        <dd class="col-sm-7">{{ $licencia->fecha_renovacion ? $licencia->fecha_renovacion->format('d/m/Y') : 'N/A' }}</dd>
                        @endif

                        <dt class="col-sm-5 text-muted">Requiere Correo:</dt>
                        <dd class="col-sm-7">{{ $licencia->requiere_correo ? 'Sí' : 'No' }}</dd>

                    @elseif($licencia->tipo_licencia === 'Vitalicia')
                        <!-- Se ha eliminado usuario_asignado -->
                    @endif
                    
                    <dt class="col-sm-5 text-muted">Correo Compra:</dt>
                    <dd class="col-sm-7">{{ $licencia->correo_compra ?: 'N/A' }}</dd>
                    
                    <dt class="col-sm-5 text-muted">Fecha Compra:</dt>
                    <dd class="col-sm-7">{{ $licencia->fecha_compra ? $licencia->fecha_compra->format('d/m/Y') : 'N/A' }}</dd>
                </dl>
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
                <button class="nav-link" id="seriales-tab" data-bs-toggle="tab" data-bs-target="#seriales" type="button" role="tab">
                    <i class="bi bi-123 me-1"></i>Seriales
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
                                <th>Serial / Correo Activación</th>
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
                                        <span class="text-muted small">Equipo SN: {{ $asignacion->equipo->serial ?? 'N/A' }}</span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>Serial:</strong> {{ $asignacion->serial ? $asignacion->serial->serial : 'N/A' }}<br>
                                    <strong>Correo:</strong> {{ $asignacion->correo_activacion ?: 'N/A' }}
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

            <!-- Pestaña Seriales -->
            <div class="tab-pane fade" id="seriales" role="tabpanel">
                <div class="mb-3">
                    <form method="POST" action="{{ route('licencias.seriales.store', $licencia) }}" class="row g-2 align-items-center">
                        @csrf
                        <div class="col-12 col-md-5">
                            <input type="text" name="serial" class="form-control form-control-sm" placeholder="Ingresar nuevo serial" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <select name="estado" class="form-select form-select-sm" required>
                                <option value="Disponible">Disponible</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-plus-circle me-1"></i>Agregar Serial
                            </button>
                        </div>
                    </form>
                </div>
                
                @if($licencia->seriales && $licencia->seriales->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Serial</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($licencia->seriales as $serialItem)
                            <tr>
                                <td class="fw-medium font-monospace">{{ $serialItem->serial }}</td>
                                <td>
                                    <span class="badge bg-{{ $serialItem->estado === 'Disponible' ? 'success' : ($serialItem->estado === 'Asignado' ? 'primary' : ($serialItem->estado === 'Reservado' ? 'warning' : 'secondary')) }}">
                                        {{ $serialItem->estado }}
                                    </span>
                                </td>
                                <td>{{ $serialItem->observaciones ?: 'Ninguna' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editSerialModal{{ $serialItem->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($serialItem->estado !== 'Asignado' && $serialItem->estado !== 'Reservado')
                                    <form method="POST" action="{{ route('licencias.seriales.destroy', [$licencia, $serialItem]) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro que desea eliminar este serial?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            
                            <!-- Edit Serial Modal -->
                            <div class="modal fade" id="editSerialModal{{ $serialItem->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('licencias.seriales.update', [$licencia, $serialItem]) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar Serial</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Serial</label>
                                                    <input type="text" name="serial" class="form-control" value="{{ $serialItem->serial }}" {{ $serialItem->estado === 'Asignado' ? 'readonly' : 'required' }}>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Estado</label>
                                                    <select name="estado" class="form-select" {{ $serialItem->estado === 'Asignado' ? 'disabled' : 'required' }}>
                                                        <option value="Disponible" {{ $serialItem->estado === 'Disponible' ? 'selected' : '' }}>Disponible</option>
                                                        <option value="Inactivo" {{ $serialItem->estado === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                                        @if($serialItem->estado === 'Asignado' || $serialItem->estado === 'Reservado')
                                                        <option value="{{ $serialItem->estado }}" selected>{{ $serialItem->estado }}</option>
                                                        @endif
                                                    </select>
                                                    @if($serialItem->estado === 'Asignado')
                                                    <input type="hidden" name="estado" value="Asignado">
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Observaciones</label>
                                                    <textarea name="observaciones" class="form-control" rows="2">{{ $serialItem->observaciones }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted mb-0">No hay seriales registrados para esta licencia.</p>
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
