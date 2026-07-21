@extends('layouts.inventario')

@section('title', 'Historial de Préstamos — ' . $equipo->nombre_equipo)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-person-fill-gear me-2 text-primary"></i>Préstamos del Equipo
        </h4>
        <small class="text-muted">{{ $equipo->nombre_equipo }} — Serial: {{ $equipo->serial }}</small>
    </div>
    <a href="{{ route('equipos.show', $equipo) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tipo Acción</th>
                        <th>Usuario</th>
                        <th>Área / Distrito</th>
                        <th>Fecha</th>
                        <th>Registrado por</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $asignacion)
                        <tr>
                            <td class="text-muted small">{{ $asignacion->id }}</td>
                            <td>
                                <span class="badge bg-{{ $asignacion->tipo_accion_color }}">
                                    {{ $asignacion->tipo_accion_label }}
                                </span>
                            </td>
                            <td>
                                @if($asignacion->usuario_nombre)
                                    <span class="fw-medium">{{ $asignacion->usuario_nombre }}</span>
                                    <br><small class="text-muted">CC: {{ $asignacion->usuario_cedula }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <small>
                                    {{ $asignacion->usuario_area ?? '—' }}
                                    @if($asignacion->usuario_distrito)
                                        <br><span class="text-muted">{{ $asignacion->usuario_distrito }}</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                <small>{{ $asignacion->fecha_accion?->format('d/m/Y H:i') ?? '—' }}</small>
                            </td>
                            <td class="small text-muted">{{ $asignacion->registradoPor?->name ?? '—' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('asignaciones.show', $asignacion) }}"
                                       class="btn btn-outline-info" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(in_array($asignacion->tipo_accion, ['asignacion','reemplazo']))
                                    <a href="{{ route('asignaciones.pdf', $asignacion) }}"
                                       class="btn btn-outline-danger" title="Descargar PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No hay registros de préstamos para este equipo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($asignaciones->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Mostrando {{ $asignaciones->firstItem() }}–{{ $asignaciones->lastItem() }} de {{ $asignaciones->total() }}
            </small>
            {{ $asignaciones->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
