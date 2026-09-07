@props(['titulo', 'relatorios', 'aberto' => false, 'relatorio'])

<div x-data="{ open: @js($aberto) }" class="rounded-lg mb-2">
    <button @click="open = !open" class="rounded-lg bg-primary text-white w-full flex justify-between items-center p-2 font-semibold hover:bg-primary-600">
        <span class="text-white">
            {{ $titulo }}
        </span>

        <i class="fa-solid fa-chevron-down" :class="{ 'rotate-180': open }"></i>
    </button>

    <div x-show="open">
        @foreach ($relatorios as $item)
            <a
                href="{{ route('relatorios.index', ['relatorio' => $item->slug()]) }}"
                @class([
                    'block px-6 py-2 transition-colors rounded-md border border-white',
                    'bg-beige-500 text-white font-medium' => $relatorio?->slug() === $item->slug(),
                    'bg-white hover:bg-gray-100' => $relatorio?->slug() !== $item->slug(),
                ])
            >
                {{ $item->titulo() }}
            </a>
        @endforeach
    </div>
</div>
