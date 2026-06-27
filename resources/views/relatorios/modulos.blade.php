<x-app-layout>
    <x-slot name="header">
        <h1>Relatórios</h1>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($modulos as $key => $item)
            <a href="{{ route('relatorios.index', $key) }}" class="bg-white rounded-lg shadow border p-6 hover:shadow-lg transition">
                <div class="text-center">
                    <i class="{{ $item['icone'] }} text-5xl text-primary"></i>

                    <h2 class="mt-4 text-xl font-semibold">
                        {{ $item['titulo'] }}
                    </h2>
                </div>
            </a>
        @endforeach
    </div>
</x-app-layout>