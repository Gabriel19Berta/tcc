@props(['type' => 'success'])

@php
    $colors = [
        'success' => 'bg-primary border-primary text-stone-50',
        'error'   => 'bg-danger-500 border-danger-500 text-stone-50',
        'warning' => 'bg-beige-600 border-beige-600 text-stone-50',
        'info'    => 'bg-info border-info text-stone-50',
    ];
@endphp

<div 
    class="flex items-center border-l-4 p-4 mb-4 rounded-md {{ $colors[$type] ?? $colors['info'] }}" role="alert">
    <div class="ms-2 text-sm font-medium">
        {{ $slot }}
    </div>

    <button type="button"
        class="ms-auto -mx-1.5 -my-1.5 rounded-md inline-flex items-center justify-center h-8 w-8 hover:bg-black/10"
         aria-label="Close">

        <span class="sr-only">Close</span>

        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
        </svg>
    </button>
</div>
