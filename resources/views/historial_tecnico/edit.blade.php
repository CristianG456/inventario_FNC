@extends('layouts.inventario')

@section('title', 'Editar Evento Técnico')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-pencil me-2 text-warning"></i>Editar Evento Técnico
    </h4>
    <a href="{{ route('historial-tecnico.show', $historialTecnico) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('historial-tecnico.update', $historialTecnico) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Equipo</label>
                    <input type="hidden" name="equipo_id" value="{{ $historialTecnico->equipo_id }}">
                    <input type="text" class="form-control" readonly
                           value="{{ $historialTecnico->equipo?->nombre_equipo }} ({{ $historialTecnico->equipo?->serial }})">
                </div>

                <div class="col-md-6">
                    <label for="tipo_evento" class="form-label fw-medium">
                        Tipo de Evento <span class="text-danger">*</span>
                    </label>
                    <select name="tipo_evento" id="tipo_evento"
                            class="form-select @error('tipo_evento') is-invalid @enderror" required>
                        @foreach($tiposEvento as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('tipo_evento', $historialTecnico->tipo_evento) === $key ? 'selected' : '' }}>
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

                <div class="col-md-6">
                    <label for="fecha_evento" class="form-label fw-medium">
                        Fecha del Evento <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="fecha_evento" id="fecha_evento"
                           class="form-control @error('fecha_evento') is-invalid @enderror"
                           value="{{ old('fecha_evento', $historialTecnico->fecha_evento?->format('Y-m-d')) }}"
                           max="{{ date('Y-m-d') }}" required>
                    @error('fecha_evento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="usuario_responsable" class="form-label fw-medium">
                        Técnico Responsable <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="usuario_responsable" id="usuario_responsable"
                           class="form-control @error('usuario_responsable') is-invalid @enderror"
                           value="{{ old('usuario_responsable', $historialTecnico->usuario_responsable) }}"
                           maxlength="150" required>
                    @error('usuario_responsable')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="observaciones" class="form-label fw-medium">Observaciones <span class="text-danger">*</span></label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="form-control @error('observaciones') is-invalid @enderror" required>{{ old('observaciones', $historialTecnico->observaciones) }}</textarea>
                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning text-white">
                    <i class="bi bi-check-lg me-1"></i>Actualizar Evento
                </button>
                <a href="{{ route('historial-tecnico.show', $historialTecnico) }}"
                   class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
