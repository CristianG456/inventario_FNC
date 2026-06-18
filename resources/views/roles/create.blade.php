@extends('layouts.inventario')

@section('title', 'Crear Rol')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Crear Nuevo Rol</h4>
    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="name" class="form-label fw-bold">Nombre del Rol <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}" placeholder="Ej: Auditor">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Asignar Permisos</h5>
                
                <div class="row g-4">
                    @foreach($permissions as $module => $modulePermissions)
                    <div class="col-md-6 col-lg-4">
                        <div class="card bg-light h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-uppercase fw-bold text-primary mb-3">{{ str_replace('_', ' ', $module) }}</h6>
                                
                                <div class="d-flex flex-column gap-2">
                                    @foreach($modulePermissions as $permission)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ explode('.', $permission->name)[1] ?? $permission->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('permissions')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-end border-top pt-4">
                <a href="{{ route('roles.index') }}" class="btn btn-light me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Guardar Rol
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
