@extends('layouts.inventario')

@section('title', 'Nueva Asignación de Licencia')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-link-45deg text-primary me-2"></i>Asignar Licencia
    </h4>
    <a href="{{ route('licencia-asignaciones.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('licencia-asignaciones.store') }}">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-12">
                    <label class="form-label fw-medium">Licencia <span class="text-danger">*</span></label>
                    <select name="licencia_id" class="form-select @error('licencia_id') is-invalid @enderror" required>
                        <option value="">Seleccione una licencia...</option>
                        @foreach($licencias as $licencia)
                            <option value="{{ $licencia->id }}" {{ old('licencia_id') == $licencia->id ? 'selected' : '' }} {{ $licencia->cupos_disponibles <= 0 ? 'disabled' : '' }}>
                                {{ $licencia->nombre }} (Cupos disponibles: {{ $licencia->cupos_disponibles }})
                            </option>
                        @endforeach
                    </select>
                    @error('licencia_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Funcionario <span class="text-danger">*</span></label>
                    <select name="funcionario_id" class="form-select @error('funcionario_id') is-invalid @enderror" required>
                        <option value="">Seleccione un funcionario...</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}" {{ old('funcionario_id') == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->nombre_completo }} ({{ $funcionario->cedula }})
                            </option>
                        @endforeach
                    </select>
                    @error('funcionario_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Equipo <span class="text-danger">*</span></label>
                    <select name="equipo_id" class="form-select @error('equipo_id') is-invalid @enderror" required>
                        <option value="">Seleccione un equipo...</option>
                        @foreach($equipos as $equipo)
                            <option value="{{ $equipo->id }}" {{ old('equipo_id') == $equipo->id ? 'selected' : '' }}>
                                {{ $equipo->nombre_equipo }} - Placa: {{ $equipo->activo_fijo ?? 'N/A' }} (Serial: {{ $equipo->serial ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('equipo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-medium">Fecha de Asignación <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_asignacion" class="form-control @error('fecha_asignacion') is-invalid @enderror" value="{{ old('fecha_asignacion', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                    @error('fecha_asignacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-medium">Fecha de Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror" value="{{ old('fecha_vencimiento') }}">
                    @error('fecha_vencimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-medium">Estado <span class="text-danger">*</span></label>
                    <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                        <option value="Activa" {{ old('estado', 'Activa') == 'Activa' ? 'selected' : '' }}>Activa</option>
                        <option value="Vencida" {{ old('estado') == 'Vencida' ? 'selected' : '' }}>Vencida</option>
                        <option value="Retirada" {{ old('estado') == 'Retirada' ? 'selected' : '' }}>Retirada</option>
                        <option value="Suspendida" {{ old('estado') == 'Suspendida' ? 'selected' : '' }}>Suspendida</option>
                    </select>
                    @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Observaciones</label>
                    <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="2">{{ old('observaciones') }}</textarea>
                    @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-2"></i>Guardar Asignación
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
