@props([
    'cancelUrl' => null,
    'submitText' => 'Cadastrar',
    'cancelText' => 'Cancelar'
])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-lg mt-2']) }}>
    <div class="p-4 text-gray-900">
        <div class="flex justify-between">
            <x-action-link :href="$cancelUrl" color="danger">
                {{ $cancelText }}
            </x-action-link>

            <x-primary-button type="submit">
                {{ $submitText }}
            </x-primary-button>
        </div>
    </div>
</div>
