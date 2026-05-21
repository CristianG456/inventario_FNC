@extends('layouts.inventario')

@section('title', 'Nuevo Equipo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-plus-circle me-2 text-primary"></i>Registrar Nuevo Equipo
    </h4>
    <a href="{{ route('equipos.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<form method="POST" action="{{ route('equipos.store') }}" novalidate id="formEquipo">
    @csrf
    @php $equipo = new \App\Models\Equipo(); @endphp
    @include('equipos._form')

    <div class="d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('equipos.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-floppy me-1"></i>Guardar Equipo
        </button>
    </div>
</form>
@endsection
