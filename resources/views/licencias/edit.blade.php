@extends('layouts.inventario')

@section('title', 'Editar Licencia')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-pencil-square text-primary me-2"></i>Editar Licencia
    </h4>
    <a href="{{ route('licencias.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('licencias.update', $licencia) }}">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nombre de Licencia <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $licencia->nombre) }}" required>
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium">Tipo <span class="text-danger">*</span></label>
                    <select name="tipo_licencia" class="form-select @error('tipo_licencia') is-invalid @enderror" required>
                        <option value="Suscripción" {{ old('tipo_licencia', $licencia->tipo_licencia) == 'Suscripción' ? 'selected' : '' }}>Suscripción</option>
                        <option value="Vitalicia" {{ old('tipo_licencia', $licencia->tipo_licencia) == 'Vitalicia' ? 'selected' : '' }}>Vitalicia</option>
                    </select>
                    @error('tipo_licencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-medium">Cantidad Máxima de Cupos <span class="text-danger">*</span></label>
                    <input type="number" name="cantidad_maxima" class="form-control @error('cantidad_maxima') is-invalid @enderror" value="{{ old('cantidad_maxima', $licencia->cantidad_maxima) }}" min="1" required>
                    @error('cantidad_maxima') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-medium">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio', $licencia->fecha_inicio ? $licencia->fecha_inicio->format('Y-m-d') : '') }}" min="{{ $licencia->fecha_inicio && $licencia->fecha_inicio->isPast() ? $licencia->fecha_inicio->format('Y-m-d') : date('Y-m-d') }}">
                    @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-medium">Fecha de Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror" value="{{ old('fecha_vencimiento', $licencia->fecha_vencimiento ? $licencia->fecha_vencimiento->format('Y-m-d') : '') }}">
                    @error('fecha_vencimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium">Estado <span class="text-danger">*</span></label>
                    <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                        <option value="Activa" {{ old('estado', $licencia->estado) == 'Activa' ? 'selected' : '' }}>Activa</option>
                        <option value="Suspendida" {{ old('estado', $licencia->estado) == 'Suspendida' ? 'selected' : '' }}>Suspendida</option>
                        <option value="Vencida" {{ old('estado', $licencia->estado) == 'Vencida' ? 'selected' : '' }}>Vencida</option>
                    </select>
                    @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Descripción</label>
                    <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion', $licencia->descripcion) }}</textarea>
                    @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-2"></i>Actualizar Licencia
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
