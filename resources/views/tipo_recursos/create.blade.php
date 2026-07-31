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
            
            <div class="mb-3">
                <label class="form-label fw-medium">Complementos Definidos</label>
                <div class="card shadow-sm border-0">
                    <div class="card-body bg-light rounded">
                        <p class="small text-muted mb-2">Seleccione los complementos que aplican para este tipo de recurso. Puede ordenar arrastrando (no implementado en esta versión básica).</p>
                        @if($catalogoComplementos->isEmpty())
                            <div class="alert alert-warning mb-0 small">No hay complementos en el catálogo.</div>
                        @else
                            <div class="row g-2">
                                @foreach($catalogoComplementos as $comp)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="complementos_ids[]" value="{{ $comp->id }}" id="comp{{ $comp->id }}">
                                            <label class="form-check-label" for="comp{{ $comp->id }}">
                                                {{ $comp->nombre }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
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
