@extends('layouts.inventario')

@section('title', 'Detalle de Solicitud')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-file-earmark-text me-2 text-primary"></i>
        Detalle de Solicitud #{{ $solicitud->id }}
    </h4>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="small text-muted">Nombre del usuario</div>
                <div class="fw-semibold">{{ $solicitud->usuario?->name ?? 'Usuario eliminado' }}</div>
            </div>
            <div class="col-md-6">
                <div class="small text-muted">Correo</div>
                <div class="fw-semibold">{{ $solicitud->email }}</div>
            </div>
            <div class="col-md-4">
                <div class="small text-muted">Fecha</div>
                <div>{{ optional($solicitud->created_at)->format('d/m/Y') }}</div>
            </div>
            <div class="col-md-4">
                <div class="small text-muted">Hora</div>
                <div>{{ optional($solicitud->created_at)->format('H:i:s') }}</div>
            </div>
            <div class="col-md-4">
                <div class="small text-muted">Estado</div>
                <div>{{ $solicitud->estado }}</div>
            </div>
            <div class="col-md-6">
                <div class="small text-muted">Dirección IP</div>
                <div>{{ $solicitud->ip ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
                <div class="small text-muted">Navegador</div>
                <div>{{ $solicitud->user_agent ?? 'N/A' }}</div>
            </div>
            <div class="col-md-12">
                <div class="small text-muted">Observaciones</div>
                <div>{{ $solicitud->observacion ?: 'Sin observaciones.' }}</div>
            </div>
            @if($solicitud->administrador)
                <div class="col-md-6">
                    <div class="small text-muted">Administrador responsable</div>
                    <div>{{ $solicitud->administrador->name }}</div>
                </div>
                <div class="col-md-6">
                    <div class="small text-muted">Fecha de atención</div>
                    <div>{{ optional($solicitud->fecha_atencion)->format('d/m/Y H:i:s') }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body d-flex flex-wrap gap-2">
        @if($solicitud->estado === 'Pendiente')
            <a href="{{ route('solicitudes-password.edit-password', $solicitud) }}" class="btn btn-primary">
                Cambiar contraseña
            </a>

            <button class="btn btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#form-rechazo" aria-expanded="false" aria-controls="form-rechazo">
                Rechazar solicitud
            </button>
        @endif

        <a href="{{ route('solicitudes-password.index') }}" class="btn btn-secondary">
            Cancelar
        </a>
    </div>
</div>

@if($solicitud->estado === 'Pendiente')
<div class="collapse mt-3" id="form-rechazo">
    <div class="card card-body">
        <form method="POST" action="{{ route('solicitudes-password.reject', $solicitud) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="observacion" class="form-label fw-semibold">Motivo del rechazo</label>
                <textarea name="observacion" id="observacion" rows="4" class="form-control @error('observacion') is-invalid @enderror" required>{{ old('observacion') }}</textarea>
                @error('observacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-danger">Confirmar rechazo</button>
        </form>
    </div>
</div>
@endif
@endsection
