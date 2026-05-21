@extends('layouts.inventario')

@section('title', 'Editar Equipo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-pencil me-2 text-warning"></i>Editar Equipo: {{ $equipo->nombre_equipo }}
    </h4>
    <a href="{{ route('equipos.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<form method="POST" action="{{ route('equipos.update', $equipo) }}" novalidate id="formEquipo">
    @csrf
    @method('PUT')
    @include('equipos._form')

    <div class="d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('equipos.show', $equipo) }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-floppy me-1"></i>Actualizar Equipo
        </button>
    </div>
</form>
@endsection
