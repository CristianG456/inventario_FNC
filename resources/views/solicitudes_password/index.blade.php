@extends('layouts.inventario')

@section('title', 'Solicitudes de Cambio de Contraseña')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-shield-exclamation me-2 text-primary"></i>
        Solicitudes de Cambio de Contraseña
    </h4>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('solicitudes-password.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="estado" class="form-label fw-semibold">Estado</label>
                <select name="estado" id="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="Pendiente" {{ request('estado') === 'Pendiente' ? 'selected' : '' }}>Pendientes</option>
                    <option value="Atendida" {{ request('estado') === 'Atendida' ? 'selected' : '' }}>Atendidas</option>
                    <option value="Rechazada" {{ request('estado') === 'Rechazada' ? 'selected' : '' }}>Rechazadas</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="fecha" class="form-label fw-semibold">Fecha</label>
                <input type="date" name="fecha" id="fecha" value="{{ request('fecha') }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label for="buscar" class="form-label fw-semibold">Buscar (nombre, correo o estado)</label>
                <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Ej: Juan, correo@dominio.com, Pendiente">
            </div>

            <div class="col-md-2 d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Filtrar
                </button>
                <a href="{{ route('solicitudes-password.index') }}" class="btn btn-outline-secondary">
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $solicitud)
                        @php
                            $claseEstado = match ($solicitud->estado) {
                                'Atendida' => 'is-atendida',
                                'Rechazada' => 'is-rechazada',
                                default => 'is-pendiente',
                            };
                        @endphp
                        <tr>
                            <td class="fw-semibold">#{{ $solicitud->id }}</td>
                            <td>{{ $solicitud->usuario?->name ?? 'Usuario eliminado' }}</td>
                            <td>{{ $solicitud->email }}</td>
                            <td>{{ optional($solicitud->created_at)->format('d/m/Y') }}</td>
                            <td>{{ optional($solicitud->created_at)->format('H:i:s') }}</td>
                            <td>
                                <span class="solicitud-password-badge {{ $claseEstado }}">{{ $solicitud->estado }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('solicitudes-password.show', $solicitud) }}" class="btn btn-sm btn-outline-primary">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No hay solicitudes registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $solicitudes->links('pagination::bootstrap-5') }}
</div>
@endsection
