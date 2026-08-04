@extends('layouts.inventario')

@section('title', 'Editar Equipo')

@section('content')
<x-ui.toolbar 
    title="Editar Equipo: {{ $equipo->nombre_equipo }}" 
    icon="pencil" 
    backRoute="{{ route('equipos.index') }}"
/>

<form method="POST" action="{{ route('equipos.update', $equipo) }}" novalidate id="formEquipo">
    @csrf
    @method('PUT')
    @include('equipos._form')

    <div class="d-flex justify-content-end gap-2 mt-2">
        <x-ui.button href="{{ route('equipos.show', $equipo) }}" color="secondary" text="Cancelar" />
        <x-ui.button type="submit" color="primary" icon="floppy" text="Actualizar Equipo" />
    </div>
</form>
@endsection
