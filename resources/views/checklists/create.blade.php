@extends('layouts.inventario')

@section('title', 'Nuevo Checklist')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-plus-circle me-2 text-primary"></i>Nuevo Checklist Técnico
    </h4>
    <a href="{{ route('checklists.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<form method="POST" action="{{ route('checklists.store') }}" novalidate>
    @csrf
    @php $checklist = new \App\Models\Checklist(); @endphp
    @include('checklists._form')

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('checklists.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-floppy me-1"></i>Guardar
        </button>
    </div>
</form>
@endsection
