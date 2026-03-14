@props(['type' => 'success'])

@php
    $colors = [
        'success' => 'bg-primary border-primary text-stone-50',
        'error'   => 'bg-danger-500 border-danger-500 text-stone-50',
        'warning' => 'bg-beige-600 border-beige-600 text-stone-50',
        'info'    => 'bg-info border-info text-stone-50',
    ];

    $alertId = 'alert-' . uniqid();
@endphp

<div id="{{ $alertId }}" class="flex items-center border-l-4 p-4 mb-4 rounded-md {{ $colors[$type] ?? $colors['info'] }}" role="alert">
    <div class="ms-2 text-sm font-medium">
        {{ $slot }}
    </div>
    <button type="button" data-dismiss-target="#{{ $alertId }}" class="ms-auto -mx-1.5 -my-1.5 rounded-md inline-flex items-center justify-center h-8 w-8 hover:bg-black/10" aria-label="Fechar">
        <span class="sr-only">Fechar</span>
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
