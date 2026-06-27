<x-app-layout>
    <x-slot name="header">
        <h1>
            Relatórios - {{ $config['titulo'] }}
        </h1>
    </x-slot>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                Relatórios - {{ $config['titulo'] }}
            </h1>
            <x-action-link href="{{ route('relatorios.modulos') }}" color="beige">
                Voltar
            </x-action-link>
        </div>
    </x-slot>

    <div class="flex gap-2 items-stretch">
        {{-- MENU LATERAL --}}
        <aside class="flex">
            <div class="bg-white rounded-lg shadow border">
                <div class="p-4">
                    <h2 class="mt-1">
                        Tipos
                    </h2>
                </div>

                <div class="p-2">
                    @foreach($config['relatorios'] as $key => $item)
                        <a href="{{ route('relatorios.index', ['modulo' => $modulo, 'relatorio' => $key]) }}"
                            class="block rounded-md px-3 py-2 mb-1 {{ request('relatorio', 'geral') == $key
                                ? 'bg-primary text-white'
                                : 'hover:bg-gray-100' }}">

                            {{ $item['titulo'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- FILTROS --}}
        <section class="flex-1">
            <form
                action="{{ route('relatorios.gerar') }}"
                method="POST"
                target="_blank"
                class="bg-white rounded-lg shadow border">

                @csrf

                <input type="hidden" name="modulo" value="{{ $modulo }}">

                <input type="hidden" name="relatorio" value="{{ request('relatorio','geral') }}">

                <div class="p-4">
                    <h2 class="mt-1">Filtros</h2>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        @if (!empty($config['relatorios'][request('relatorio','geral')]['filtros']))
                                @foreach($config['relatorios'][request('relatorio','geral')]['filtros'] as $filtro)
                                @include("relatorios.filtros.$filtro")
                            @endforeach
                        @endif
                    </div>

                    <x-input-label for="formato" :value="__('Formato')" />

                    <div class="mt-2 flex gap-6">
                        <x-input-label>
                            <input type="radio" name="formato" value="excel" checked>
                            Excel
                        </x-input-label>

                        <x-input-label>
                            <input type="radio" name="formato" value="pdf">
                            PDF
                        </x-input-label>
                    </div>
                    <div class="mt-8 flex justify-end">
                        <x-primary-button>Gerar Relatório</x-primary-button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>