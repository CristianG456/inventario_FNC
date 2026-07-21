@extends('layouts.inventario')

@section('title', 'Nuevo Checklist')

@section('content')
@php
    $equipoBackId = old('equipo_id', request('equipo_id'));
    $volverUrl = $equipoBackId ? route('equipos.edit', $equipoBackId) : route('checklists.index');
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-plus-circle me-2 text-primary"></i>Nuevo Checklist Técnico
    </h4>
    <a href="{{ $volverUrl }}" class="btn btn-outline-secondary" id="btnVolverChecklist">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<form method="POST" action="{{ route('checklists.store') }}" novalidate>
    @csrf
    @php $checklist = new \App\Models\Checklist(); @endphp
    @include('checklists._form')

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ $volverUrl }}" class="btn btn-secondary" id="btnCancelarChecklist">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-floppy me-1"></i>Guardar
        </button>
    </div>
</form>

<script>
    (() => {
        const equipoSelect = document.querySelector('select[name="equipo_id"]');
        if (!equipoSelect) return;

        const defaultUrl = @json(route('checklists.index'));
        const equipoEditTemplate = @json(route('equipos.edit', ['equipo' => '__ID__']));
        const targets = [
            document.getElementById('btnVolverChecklist'),
            document.getElementById('btnCancelarChecklist'),
        ].filter(Boolean);

        const updateBackLinks = () => {
            const id = equipoSelect.value;
            const href = id ? equipoEditTemplate.replace('__ID__', id) : defaultUrl;
            targets.forEach((el) => {
                el.setAttribute('href', href);
            });
        };

        equipoSelect.addEventListener('change', updateBackLinks);
        updateBackLinks();
    })();
</script>
@endsection
