@extends('layouts.inventario')

@section('title', 'Nuevo Tipo de Recurso')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-plus-circle me-2 text-primary"></i>Nuevo Tipo de Recurso
    </h4>
    <a href="{{ route('tipo-recursos.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card config-card-narrow">
    <div class="card-body">
        <form method="POST" action="{{ route('tipo-recursos.store') }}" novalidate>
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre"
                       class="form-control @error('nombre') is-invalid @enderror"
                       value="{{ old('nombre') }}"
                       required maxlength="100"
                       placeholder="Ej: Laptop, PC, Tablet...">
                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('tipo-recursos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-1"></i>Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
