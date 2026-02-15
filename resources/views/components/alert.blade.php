@props(['type' => 'success'])

@php
    $colors = [
        'success' => 'bg-primary border-primary',
        'error'   => 'bg-danger-500 border-danger-500',
        'warning' => 'bg-beige-600 border-beige-600',
        'info'    => 'bg-info border-info',
    ];
@endphp

<div class="border-l-4 p-4 rounded-md {{ $colors[$type] ?? $colors['info'] }}">
    <p class="text-sm font-medium text-stone-50">
        {{ $slot }}
    </p>
</div>
