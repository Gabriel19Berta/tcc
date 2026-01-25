@php
    $baseClasses = 'inline-flex items-center px-4 py-2 border border-transparent rounded-md
                    font-semibold text-xs text-white uppercase tracking-widest
                    transition ease-in-out duration-150';

    $colors = [
        'primary' => 'bg-primary hover:bg-primary-600 active:bg-primary-600',
        'beige' => 'bg-beige-600 hover:bg-beige-700 active:bg-beige-700',
        'danger'  => 'bg-red-600 hover:bg-red-700 active:bg-red-700',
    ];

    $colorClasses = $colors[$color] ?? $colors['primary'];
@endphp

<a {{ $attributes->merge([
        'class' => "$baseClasses $colorClasses"
    ]) }}>
    {{ $slot }}
</a>