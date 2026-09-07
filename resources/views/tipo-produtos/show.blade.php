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
        <div class="bg-white rounded-lg overflow-hidden">
            <x-show-table>
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
                <tr>
                    <th>Criado em</th>
                    <td>{{ $tipoProduto->created_at->format('d/m/Y H:i:s') }}</td>
                </tr>
                <tr>
                    <th>Modificado em</th>
                    <td>{{ $tipoProduto->updated_at->format('d/m/Y H:i:s') }}</td>
            </x-show-table>
        </div>
    </div>
</x-app-layout>