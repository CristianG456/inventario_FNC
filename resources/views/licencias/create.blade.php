@extends('layouts.inventario')

@section('title', 'Nueva Licencia')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-key text-primary me-2"></i>Nueva Licencia
    </h4>
    <a href="{{ route('licencias.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('licencias.store') }}">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nombre de Licencia <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium">Tipo <span class="text-danger">*</span></label>
                    <select name="tipo_licencia" id="tipo_licencia" class="form-select @error('tipo_licencia') is-invalid @enderror" required>
                        <option value="Suscripción" {{ old('tipo_licencia') == 'Suscripción' ? 'selected' : '' }}>Suscripción</option>
                        <option value="Vitalicia" {{ old('tipo_licencia') == 'Vitalicia' ? 'selected' : '' }}>Licencia Vitalicia</option>
                    </select>
                    @error('tipo_licencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium">Estado <span class="text-danger">*</span></label>
                    <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                        <option value="Activa" {{ old('estado') == 'Activa' ? 'selected' : '' }}>Activa</option>
                        <option value="Suspendida" {{ old('estado') == 'Suspendida' ? 'selected' : '' }}>Suspendida</option>
                        <option value="Vencida" {{ old('estado') == 'Vencida' ? 'selected' : '' }}>Vencida</option>
                    </select>
                    @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Campos de Suscripción -->
                <div class="col-md-4 campo-suscripcion">
                    <label class="form-label fw-medium">Cantidad Máxima de Cupos <span class="text-danger">*</span></label>
                    <input type="number" name="cantidad_maxima" id="cantidad_maxima" class="form-control @error('cantidad_maxima') is-invalid @enderror" value="{{ old('cantidad_maxima', 1) }}" min="1">
                    @error('cantidad_maxima') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-4 campo-suscripcion">
                    <label class="form-label fw-medium">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio') }}">
                    @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-4 campo-suscripcion">
                    <label class="form-label fw-medium">Fecha de Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror" value="{{ old('fecha_vencimiento') }}">
                    @error('fecha_vencimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 campo-suscripcion">
                    <label class="form-label fw-medium">¿Requiere correo electrónico? <span class="text-danger">*</span></label>
                    <select name="requiere_correo" id="requiere_correo" class="form-select @error('requiere_correo') is-invalid @enderror">
                        <option value="0" {{ old('requiere_correo') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('requiere_correo') == '1' ? 'selected' : '' }}>Sí</option>
                    </select>
                    @error('requiere_correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Campo compartido o condicional (Correo Compra) -->
                <div class="col-md-6" id="container_correo_asociado" style="display: none;">
                    <label class="form-label fw-medium">Correo de Compra <span class="text-danger">*</span></label>
                    <input type="email" name="correo_compra" id="correo_compra" class="form-control @error('correo_compra') is-invalid @enderror" value="{{ old('correo_compra') }}" placeholder="Correo con el que se adquirió">
                    @error('correo_compra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Fecha de Compra</label>
                    <input type="date" name="fecha_compra" class="form-control @error('fecha_compra') is-invalid @enderror" value="{{ old('fecha_compra') }}">
                    <div class="form-text">Informativa</div>
                    @error('fecha_compra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3 campo-suscripcion">
                    <label class="form-label fw-medium">Fecha de Renovación</label>
                    <input type="date" name="fecha_renovacion" class="form-control @error('fecha_renovacion') is-invalid @enderror" value="{{ old('fecha_renovacion') }}">
                    @error('fecha_renovacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Campos de Vitalicia -->
                <div class="col-md-6 campo-vitalicia" style="display: none;">
                    <!-- Se ha eliminado usuario_asignado -->
                </div>

            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-2"></i>Guardar Licencia
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo_licencia');
    const requiereCorreoSelect = document.getElementById('requiere_correo');
    
    const camposSuscripcion = document.querySelectorAll('.campo-suscripcion');
    const camposVitalicia = document.querySelectorAll('.campo-vitalicia');
    const containerCorreo = document.getElementById('container_correo_asociado');
    
    function updateForm() {
        const tipo = tipoSelect.value;
        const requiereCorreo = requiereCorreoSelect.value === '1';
        
        if (tipo === 'Suscripción') {
            camposSuscripcion.forEach(el => el.style.display = 'block');
            camposVitalicia.forEach(el => el.style.display = 'none');
            
            if (requiereCorreo) {
                containerCorreo.style.display = 'block';
            } else {
                containerCorreo.style.display = 'none';
            }
        } else if (tipo === 'Vitalicia') {
            camposSuscripcion.forEach(el => el.style.display = 'none');
            camposVitalicia.forEach(el => el.style.display = 'block');
            
            // Vitalicia always requires correo_asociado according to requirements
            containerCorreo.style.display = 'block';
        }
    }
    
    tipoSelect.addEventListener('change', updateForm);
    requiereCorreoSelect.addEventListener('change', updateForm);
    
    // Initial load
    updateForm();
});
</script>
@endpush
@endsection
