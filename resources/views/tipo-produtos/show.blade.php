<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Dados do Tipo Produtos') }}
            </h1>

            <x-action-link href="{{ route('tipo-produtos.index') }}">
                Voltar
            </x-action-link>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg overflow-hidden">
                <x-show-table>
                    <tbody>
                        <tr class="bg-gray-50">
                            <th colspan="2" scope="colgroup" class="text-base text-primary">
                                Dados Básicos
                            </th>
                        </tr>
                        <tr>
                            <th>Código</th>
                            <td>{{ $tipoProduto->id }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $tipoProduto->status_formatada }}</td>
                        </tr>
                        <tr>
                            <th>Nome</th>
                            <td>{{ $tipoProduto->nome }}</td>
                        </tr>
                    </tbody>
                </x-show-table>
            </div>
        </div>
    </div>
</x-app-layout>