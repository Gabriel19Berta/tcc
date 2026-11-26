@props([
    'disabled' => false,
    'showError' => true,
    'errorBag' => 'default',
])

@php
    $name = $attributes->get('name');
    $erros = $errors->getBag($errorBag);
    $hasError = $name && $erros->has($name);

    // Campos de senha ganham o botão de exibir/ocultar.
    $senha = $attributes->get('type') === 'password';
@endphp

<div>
    @if ($senha)
        <div class="relative" data-campo-senha>
    @endif

    <input
        @disabled($disabled)
        {{ $attributes->merge([
            'class' =>
                'p-[6px] border rounded-md shadow-sm focus:outline-none focus:ring-1 ' .
                ($senha ? 'pr-10 ' : '') .
                ($hasError && $showError
                    ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500'
                    : 'border-gray-300 focus:border-primary focus:ring-primary'
                )
        ]) }}
    >

    @if ($senha)
            {{-- Alternância controlada por resources/js/senha.js --}}
            <button type="button" data-alternar-senha
                class="absolute bottom-0 right-0 flex h-[38px] w-10 items-center justify-center text-gray-500 hover:text-primary focus:outline-none focus:text-primary"
                aria-label="Exibir senha" title="Exibir senha" aria-pressed="false"
                @disabled($disabled)
            >
                <i class="fa-solid fa-eye-slash" aria-hidden="true"></i>
            </button>
        </div>
    @endif

    @if ($hasError && $showError)
        <p class="mt-1 text-sm text-danger-500 input-error">
            {{ $erros->first($name) }}
        </p>
    @endif
</div>
