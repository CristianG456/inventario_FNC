@extends('layouts.inventario')

@section('title', isset($usuario) ? 'Editar Usuario' : 'Crear Usuario')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">{{ isset($usuario) ? 'Editar Usuario: ' . $usuario->name : 'Crear Nuevo Usuario' }}</h4>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ isset($usuario) ? route('usuarios.update', $usuario->id) : route('usuarios.store') }}" method="POST">
            @csrf
            @if(isset($usuario))
                @method('PUT')
            @endif

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $usuario->name ?? '') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label fw-bold">Correo Electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $usuario->email ?? '') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label fw-bold">
                        Contraseña 
                        @if(isset($usuario)) <span class="text-muted fw-normal small">(Dejar en blanco para mantener la actual)</span> @endif
                    </label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" {{ isset($usuario) ? '' : 'required' }}>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="role" class="form-label fw-bold">Rol del Sistema <span class="text-danger">*</span></label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">Seleccione un rol...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ (old('role', isset($usuario) ? ($usuario->roles->first()->name ?? '') : '') == $role->name) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="text-end border-top pt-4">
                <a href="{{ route('usuarios.index') }}" class="btn btn-light me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>{{ isset($usuario) ? 'Actualizar Usuario' : 'Guardar Usuario' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
