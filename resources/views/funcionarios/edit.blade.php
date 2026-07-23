@extends('layouts.inventario')

@section('title', 'Editar Funcionario')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Editar Funcionario</h4>
                    <p class="text-muted mb-0">Actualizar datos del funcionario</p>
                </div>
                <a href="{{ route('funcionarios.show', $funcionario) }}" class="btn btn-light rounded-circle" title="Cerrar">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>

        <form action="{{ route('funcionarios.update', $funcionario) }}" method="POST" class="card border-0 shadow-sm p-4">
            @csrf
            @method('PUT')

            <h6 class="text-uppercase fw-bold text-dark mb-4 pb-2 border-bottom d-flex align-items-center gap-2 funcionario-section-title">
                <i class="bi bi-person text-primary"></i> DATOS PERSONALES
            </h6>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark">Identificación (Cédula) <span class="text-danger">*</span></label>
                    <input type="text" name="identificacion" class="form-control @error('identificacion') is-invalid @enderror" required value="{{ old('identificacion', $funcionario->identificacion) }}">
                    @error('identificacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark">Nombres <span class="text-danger">*</span></label>
                    <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror" required value="{{ old('nombres', $funcionario->nombres) }}">
                    @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control @error('apellidos') is-invalid @enderror" value="{{ old('apellidos', $funcionario->apellidos) }}">
                    @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="text-uppercase fw-bold text-dark mb-4 mt-5 pb-2 border-bottom d-flex align-items-center gap-2 funcionario-section-title">
                <i class="bi bi-briefcase text-dark"></i> DATOS DE VINCULACIÓN
            </h6>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark">Cargo</label>
                    <input type="text" name="cargo" class="form-control @error('cargo') is-invalid @enderror" value="{{ old('cargo', $funcionario->cargo) }}">
                    @error('cargo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark">Área / Gestión</label>
                    <input type="text" name="area" class="form-control @error('area') is-invalid @enderror" value="{{ old('area', $funcionario->area) }}">
                    @error('area')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark">Empresa Funcionario</label>
                    <input type="text" name="empresa_funcionario" class="form-control @error('empresa_funcionario') is-invalid @enderror" value="{{ old('empresa_funcionario', $funcionario->empresa_funcionario) }}">
                    @error('empresa_funcionario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark">Tipo de Vinculación</label>
                    <input type="text" name="tipo_vinculacion" class="form-control @error('tipo_vinculacion') is-invalid @enderror" value="{{ old('tipo_vinculacion', $funcionario->tipo_vinculacion) }}">
                    @error('tipo_vinculacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <div class="row mb-5">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-dark">Departamento</label>
                    <input type="text" name="departamento" class="form-control @error('departamento') is-invalid @enderror" value="{{ old('departamento', $funcionario->departamento) }}">
                    @error('departamento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-dark">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control @error('ciudad') is-invalid @enderror" value="{{ old('ciudad', $funcionario->ciudad) }}">
                    @error('ciudad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-dark">Estado <span class="text-danger">*</span></label>
                    <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                        <option value="Activo" {{ old('estado', $funcionario->estado) === 'Activo' ? 'selected' : '' }}>Activo</option>
                        <option value="Inactivo" {{ old('estado', $funcionario->estado) === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                <a href="{{ route('funcionarios.show', $funcionario) }}" class="btn btn-light border px-4 rounded-pill">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4 rounded-pill">
                    <i class="bi bi-check2 me-1"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
