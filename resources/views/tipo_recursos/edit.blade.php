@extends('layouts.inventario')

@section('title', 'Editar Tipo de Recurso')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-pencil me-2 text-warning"></i>Editar Tipo: {{ $tipoRecurso->nombre }}
    </h4>
    <a href="{{ route('tipo-recursos.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card config-card-narrow">
    <div class="card-body">
        <form method="POST" action="{{ route('tipo-recursos.update', $tipoRecurso) }}" novalidate>
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-medium">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre"
                       class="form-control @error('nombre') is-invalid @enderror"
                       value="{{ old('nombre', $tipoRecurso->nombre) }}"
                       required maxlength="100">
                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('tipo-recursos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-1"></i>Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
