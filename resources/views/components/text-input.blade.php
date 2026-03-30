@props(['disabled' => false])

@php
    $name = $attributes->get('name');
    $hasError = $name && $errors->has($name);
@endphp

<div>
    <input
        @disabled($disabled)
        {{ $attributes->merge([
            'class' =>
                'p-[6px] input-form border rounded-md shadow-sm focus:outline-none focus:ring-1 ' .
                ($hasError
                    ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500'
                    : 'border-gray-300 focus:border-primary focus:ring-primary'
                )
        ]) }}
    >

    @if ($hasError)
        <p class="mt-1 text-sm text-danger-500 input-error">
            {{ $errors->first($name) }}
        </p>
    @endif
</div>
