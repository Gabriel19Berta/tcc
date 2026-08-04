<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Dados da Marca') }}
            </h1>

            <x-action-link href="{{ route('marcas.index') }}">
                Voltar
            </x-action-link>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="bg-white rounded-lg overflow-hidden">
            <x-show-table>
                <tr class="bg-gray-50">
                    <th colspan="2" scope="colgroup" class="text-base text-primary">
                        Dados Básicos
                    </th>
                </tr>
                <tr>
                    <th>Código</th>
                    <td>{{ $marca->id }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ $marca->status_formatada }}</td>
                </tr>
                <tr>
                    <th>Nome</th>
                    <td>{{ $marca->nome }}</td>
                </tr>
            </x-show-table>
        </div>
    </div>
</x-app-layout>