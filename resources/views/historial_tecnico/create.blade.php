@extends('layouts.inventario')

@section('title', 'Nuevo Evento Técnico')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-plus-circle me-2 text-primary"></i>Registrar Evento Técnico
    </h4>
    <a href="{{ $volverUrl }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('historial-tecnico.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                {{-- Equipo --}}
                <div class="col-md-6">
                    <label for="equipo_id" class="form-label fw-medium">
                        Equipo <span class="text-danger">*</span>
                    </label>
                    <select name="equipo_id" id="equipo_id"
                            class="form-select @error('equipo_id') is-invalid @enderror" required>
                        <option value="">Seleccione un equipo...</option>
                        @foreach($equipos as $eq)
                            <option value="{{ $eq->id }}"
                                {{ (old('equipo_id', $equipo?->id) == $eq->id) ? 'selected' : '' }}>
                                {{ $eq->nombre_equipo }} ({{ $eq->serial }})
                            </option>
                        @endforeach
                    </select>
                    @error('equipo_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tipo de evento --}}
                <div class="col-md-6">
                    <label for="tipo_evento" class="form-label fw-medium">
                        Tipo de Evento <span class="text-danger">*</span>
                    </label>
                    <select name="tipo_evento" id="tipo_evento"
                            class="form-select @error('tipo_evento') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        @foreach($tiposEvento as $key => $label)
                            <option value="{{ $key }}" {{ old('tipo_evento') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_evento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted d-block mt-1">
                        Requerimiento: solicitud que no impide trabajar. Incidente: problema que impide realizar las labores normalmente.
                    </small>
                </div>

                {{-- Fecha del evento --}}
                <div class="col-md-6">
                    <label for="fecha_evento" class="form-label fw-medium">
                        Fecha del Evento <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="fecha_evento" id="fecha_evento"
                           class="form-control @error('fecha_evento') is-invalid @enderror"
                           value="{{ old('fecha_evento', date('Y-m-d')) }}"
                           max="{{ date('Y-m-d') }}" required>
                    @error('fecha_evento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Técnico Responsable --}}
                <div class="col-md-6">
                    <label for="usuario_responsable" class="form-label fw-medium">
                        Técnico Responsable <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="usuario_responsable" id="usuario_responsable"
                           class="form-control @error('usuario_responsable') is-invalid @enderror"
                           value="{{ old('usuario_responsable', $responsableSugerido) }}"
                           maxlength="150" required>
                    @error('usuario_responsable')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Observaciones --}}
                <div class="col-12">
                    <label for="observaciones" class="form-label fw-medium">Observaciones <span class="text-danger">*</span></label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="form-control @error('observaciones') is-invalid @enderror"
                              placeholder="Describe el requerimiento o incidente..." required>{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Guardar Evento
                </button>
                <a href="{{ $volverUrl }}" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
