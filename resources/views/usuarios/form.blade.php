@extends('layouts.inventario')

@php
    $usuarioForm = $usuario ?? $user ?? null;
    $esEdicion = !is_null($usuarioForm);
@endphp

@section('title', $esEdicion ? 'Editar Usuario' : 'Crear Usuario')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">{{ $esEdicion ? 'Editar Usuario: ' . $usuarioForm->name : 'Crear Nuevo Usuario' }}</h4>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form id="usuario-form" action="{{ $esEdicion ? route('usuarios.update', $usuarioForm->id) : route('usuarios.store') }}" method="POST" autocomplete="off" novalidate>
            @csrf
            @if($esEdicion)
                @method('PUT')
            @endif

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ $esEdicion ? old('name', $usuarioForm->name ?? '') : '' }}" autocomplete="off" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label fw-bold">Correo Electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ $esEdicion ? old('email', $usuarioForm->email ?? '') : '' }}" autocomplete="off" maxlength="255" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="invalid-feedback" id="email-feedback">Debe ingresar un correo electrónico válido.</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label fw-bold">
                        Contraseña 
                        @if($esEdicion) <span class="text-muted fw-normal small">(Dejar en blanco para mantener la actual)</span> @endif
                    </label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" minlength="8" {{ $esEdicion ? '' : 'required' }}>
                    <small class="text-muted d-block mt-1">Mínimo 8 caracteres, incluyendo mayúscula, minúscula, número y símbolo.</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="invalid-feedback" id="password-feedback">La contraseña no cumple la complejidad requerida.</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label fw-bold">
                        Confirmar Contraseña @if(!$esEdicion)<span class="text-danger">*</span>@endif
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" minlength="8" {{ $esEdicion ? '' : 'required' }}>
                    <div class="invalid-feedback" id="password-confirmation-feedback">La confirmación de la contraseña no coincide.</div>
                </div>

                <div class="col-md-6">
                    <label for="role" class="form-label fw-bold">Rol del Sistema <span class="text-danger">*</span></label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">Seleccione un rol...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ ($esEdicion ? old('role', $usuarioForm->roles->first()->name ?? '') : '') == $role->name ? 'selected' : '' }}>
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
                    <i class="bi bi-save me-1"></i>{{ $esEdicion ? 'Actualizar Usuario' : 'Guardar Usuario' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('usuario-form');
        if (!form) {
            return;
        }

        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/;

        const setInvalid = function (input, message) {
            input.classList.add('is-invalid');
            input.setCustomValidity(message);
        };

        const setValid = function (input) {
            input.classList.remove('is-invalid');
            input.setCustomValidity('');
        };

        const validateEmail = function () {
            const value = (emailInput.value || '').trim();
            if (!value || emailRegex.test(value)) {
                setValid(emailInput);
                return true;
            }
            setInvalid(emailInput, 'Debe ingresar un correo electrónico válido.');
            return false;
        };

        const validatePassword = function () {
            const value = passwordInput.value || '';
            if (!value && !passwordInput.required) {
                setValid(passwordInput);
                return true;
            }
            if (passwordRegex.test(value)) {
                setValid(passwordInput);
                return true;
            }
            setInvalid(passwordInput, 'La contraseña no cumple la complejidad requerida.');
            return false;
        };

        const validatePasswordConfirmation = function () {
            const passwordValue = passwordInput.value || '';
            const confirmationValue = passwordConfirmationInput.value || '';

            if (!passwordValue && !passwordConfirmationInput.required) {
                setValid(passwordConfirmationInput);
                return true;
            }

            if (passwordValue === confirmationValue) {
                setValid(passwordConfirmationInput);
                return true;
            }

            setInvalid(passwordConfirmationInput, 'La confirmación de la contraseña no coincide.');
            return false;
        };

        emailInput.addEventListener('input', validateEmail);
        passwordInput.addEventListener('input', function () {
            validatePassword();
            validatePasswordConfirmation();
        });
        passwordConfirmationInput.addEventListener('input', validatePasswordConfirmation);

        form.addEventListener('submit', function (event) {
            const validEmail = validateEmail();
            const validPassword = validatePassword();
            const validConfirmation = validatePasswordConfirmation();

            if (!validEmail || !validPassword || !validConfirmation) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });
</script>
@endpush
