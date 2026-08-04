<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Relatórios') }}
            </h1>
        </div>
    </x-slot>

    <div class="flex gap-6">
        <div class="w-80 shrink-0">
            <x-relatorios.sidebar :modulos="$modulos"/>
        </div>

        <div class="flex-1">
            @if($relatorio)
                @include($relatorio->viewFiltros())
            @else
                <div class="p-4 bg-white rounded-lg shadow">
                    <h2 class="mt-1">Relatórios</h2>

                    <p class="mt-2">
                        Selecione um relatório no menu lateral para começar.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>