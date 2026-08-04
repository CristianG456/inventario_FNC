@props([
    'action' => '',
    'method' => 'GET',
    'clearRoute' => null,
    'id' => null,
])

<x-ui.card noPadding="true" class="mb-4">
    <div class="p-3">
        <form @if($id) id="{{ $id }}" @endif method="{{ $method }}" action="{{ $action }}" class="row g-2 align-items-end">
            
            {{ $slot }}

            <div class="col-12 col-md-2 d-flex justify-content-end gap-1">
                <x-ui.button type="submit" color="primary" class="flex-fill" icon="funnel" text="Filtrar" />
                @if($clearRoute && count(array_filter(request()->query())) > 0)
                    <x-ui.button href="{{ $clearRoute }}" color="secondary" outline="true" icon="eraser" title="Limpiar Filtros" />
                @endif
            </div>
        </form>
    </div>
</x-ui.card>
