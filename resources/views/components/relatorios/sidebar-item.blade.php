@props(['titulo', 'relatorios'])

<div x-data="{ open: false }" class="border rounded-lg bg-white mb-2">
    <button @click="open = !open" class="rounded-lg bg-primary text-white w-full flex justify-between items-center p-2 font-semibold hover:bg-primary-600">
        <span class="text-white">
            {{ $titulo }}
        </span>

        <i class="fa-solid fa-chevron-down" :class="{ 'rotate-180': open }"></i>
    </button>

    <div x-show="open">
        @foreach ($relatorios as $relatorio)
            <a href="{{ route('relatorios.index', ['relatorio' => $relatorio->slug()]) }}"
                class="block px-6 py-2 hover:bg-gray-100">

                {{ $relatorio->titulo() }}
            </a>
        @endforeach
    </div>
</div>
