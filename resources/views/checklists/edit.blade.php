@extends('layouts.inventario')

@section('title', 'Editar Checklist')

@section('content')
@php
    $equipoBackId = old('equipo_id', $checklist->equipo_id);
    $volverUrl = $equipoBackId ? route('equipos.show', $equipoBackId) : route('checklists.index');
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-pencil me-2 text-warning"></i>Editar Checklist
    </h4>
    <a href="{{ $volverUrl }}" class="btn btn-outline-secondary" id="btnVolverChecklist">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<form method="POST" action="{{ route('checklists.update', $checklist) }}" novalidate>
    @csrf
    @method('PUT')
    @include('checklists._form')

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ $volverUrl }}" class="btn btn-secondary" id="btnCancelarChecklist">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-floppy me-1"></i>Actualizar
        </button>
    </div>
</form>

<script>
    (() => {
        const equipoSelect = document.querySelector('select[name="equipo_id"]');
        if (!equipoSelect) return;

        const defaultUrl = @json(route('checklists.index'));
        const equipoShowTemplate = @json(route('equipos.show', ['equipo' => '__ID__']));
        const targets = [
            document.getElementById('btnVolverChecklist'),
            document.getElementById('btnCancelarChecklist'),
        ].filter(Boolean);

        const updateBackLinks = () => {
            const id = equipoSelect.value;
            const href = id ? equipoShowTemplate.replace('__ID__', id) : defaultUrl;
            targets.forEach((el) => {
                el.setAttribute('href', href);
            });
        };

        equipoSelect.addEventListener('change', updateBackLinks);
        updateBackLinks();
    })();
</script>
@endsection
