@use('App\Support\Formatador')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Dados do Produto') }}
            </h1>

            <x-action-link href="{{ route('produtos.index') }}">
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
                    <td>{{ $produto->id }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ Formatador::status($produto->status) }}</td>
                </tr>
                <tr>
                    <th>Nome</th>
                    <td>{{ $produto->nome }}</td>
                </tr>
                <tr>
                    <th>Marca</th>
                    <td>{{ $produto['marca']->nome ?? '' }}</td>
                </tr>
                <tr>
                    <th>Tipo de Produto</th>
                    <td>{{ $produto['tipoProduto']->nome ?? '' }}</td>
                </tr>
                <tr>
                    <th>Peso</th>
                    <td>{{ $produto->peso }}</td>
                </tr>
                <tr>
                    <th>Preço de custo</th>
                    <td>{{ Formatador::reais($produto->preco_custo) }}</td>
                </tr>
                <tr>
                    <th>Preço de venda</th>
                    <td>{{ Formatador::reais($produto->preco_venda) }}</td>
                </tr>
                <tr>
                    <th>Criado em</th>
                    <td>{{ Formatador::data($produto->created_at) }}</td>
                </tr>
                <tr>
                    <th>Modificado em</th>
                    <td>{{ Formatador::data($produto->updated_at) }}</td>
                </tr>
                <tr class="bg-gray-50">
                    <th colspan="2" scope="colgroup" class="text-base text-primary">
                        Estoque
                    </th>
                </tr>
                <tr>
                    <th>Quantidade</th>
                    <td>{{ $produto->quantidade }}</td>
                </tr>
                <tr class="bg-gray-50">
                    <th colspan="2" scope="colgroup" class="text-base text-primary">
                        Dados adicionais
                    </th>
                </tr>
                <tr>
                    <th>Observação</th>
                    <td>{{ $produto->observacoes }}</td>
                </tr>
            </x-show-table>
        </div>
        
    </div>
</x-app-layout>