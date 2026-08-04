@props([
    'title',
    'icon' => null,
    'createRoute' => null,
    'createText' => 'Nuevo',
    'exportRoute' => null,
    'importRoute' => null,
    'backRoute' => null,
])

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        @if($icon)
            <i class="bi bi-{{ $icon }} me-2 text-primary"></i>
        @endif
        {{ $title }}
        {{ $titleSuffix ?? '' }}
    </h4>
    <div class="d-flex gap-2">
        {{ $slot }}
        
        @if($backRoute)
            <x-ui.button href="{{ $backRoute }}" color="secondary" outline="true" icon="arrow-left" text="Volver" />
        @endif

        @if($exportRoute)
            <x-ui.button href="{{ $exportRoute }}" color="success" icon="file-earmark-excel" text="Exportar" />
        @endif
        
        @if($importRoute)
            <x-ui.button href="{{ $importRoute }}" color="info" class="text-white" icon="cloud-arrow-up" text="Importar" />
        @endif

        @if($createRoute)
            <x-ui.button href="{{ $createRoute }}" color="primary" icon="plus-lg" text="{{ $createText }}" />
        @endif
    </div>
</div>
