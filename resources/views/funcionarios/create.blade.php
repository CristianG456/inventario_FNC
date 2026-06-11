@extends('layouts.inventario')

@section('title', 'Nuevo Funcionario')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <div class="card mb-4 border-0 shadow-sm">
            <div class="p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Nuevo Funcionario</h4>
                    <p class="text-muted mb-0">Registrar un nuevo empleado en el directorio</p>
                </div>
                <a href="{{ route('funcionarios.index') }}" class="btn btn-light rounded-circle">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>

        <form action="{{ route('funcionarios.store') }}" method="POST" class="card border-0 shadow-sm p-4">
            @csrf

            <h6 class="text-uppercase fw-bold text-dark mb-4 pb-2 border-bottom d-flex align-items-center gap-2 funcionario-section-title">
                <i class="bi bi-person text-primary"></i> DATOS PERSONALES
            </h6>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Identificación (Cédula) <span class="text-danger">*</span></label>
                    <input type="text" name="identificacion" class="form-control" required value="{{ old('identificacion') }}">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Nombres <span class="text-danger">*</span></label>
                    <input type="text" name="nombres" class="form-control" required value="{{ old('nombres') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos') }}">
                </div>
            </div>

            <h6 class="text-uppercase fw-bold text-dark mb-4 mt-5 pb-2 border-bottom d-flex align-items-center gap-2 funcionario-section-title">
                <i class="bi bi-briefcase text-dark"></i> DATOS DE VINCULACIÓN
            </h6>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Cargo</label>
                    <input type="text" name="cargo" class="form-control" value="{{ old('cargo') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Área / Gestión</label>
                    <input type="text" name="area" class="form-control" value="{{ old('area') }}">
                </div>
            </div>
            
            <div class="row mb-5">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-dark">Departamento</label>
                    <input type="text" name="departamento" class="form-control" value="{{ old('departamento') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-dark">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-dark">Estado <span class="text-danger">*</span></label>
                    <select name="estado" class="form-select" required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                <a href="{{ route('funcionarios.index') }}" class="btn btn-light border px-4 rounded-pill">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4 rounded-pill">
                    <i class="bi bi-check2 me-1"></i> Guardar Funcionario
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
