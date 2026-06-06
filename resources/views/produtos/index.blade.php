<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Produtos') }}
            </h1>

            <div class="flex gap-2">
                <x-action-link href="{{ route('produtos.create') }}">
                    Cadastrar
                </x-action-link>
            </div>
        </div>
    </x-slot>

    <div class="py-4 mx-auto sm:px-6 lg:px-8 overflow-auto">
        
        {{-- FILTROS --}}
        <form method="GET" action="{{ route('produtos.index') }}" class="mb-4 flex flex-col lg:flex-row lg:justify-between gap-4 sm:px-0 px-4">
            <div class="flex flex-wrap gap-2 items-end">
                <div>
                    <x-input-label for="codigo" :value="__('Código')" />
                    <x-text-input id="codigo" name="codigo" type="number" min="0" :value="request('codigo')" class="w-24" />
                </div>
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status">
                        <option value="todos" {{ request('status') === 'todos' ? 'selected' : '' }}>Todos</option>
                        <option value="1" {{ request('status', '1') === '1' ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="nome" :value="__('Nome')" />
                    <x-text-input id="nome" name="nome" type="text" :value="request('nome')" class="w-96"/>
                </div>
                <div class="w-96">
                    <x-input-label for="nome" :value="__('Marca')" />
                    <select name="marca_id" id="marca_id" class="select2">
                        <option value=""></option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca['id'] }}"
                                {{ request()->get('marca_id') == $marca['id'] ? 'selected' : '' }}>
                                {{ $marca['nome'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-96">
                    <x-input-label for="nome" :value="__('Tipo de produto')" />
                    <select name="tipo_produto_id" id="tipo_produto_id" class="select2">
                        <option value=""></option>
                        @foreach ($tipo_produtos as $tipo_produto)
                            <option value="{{ $tipo_produto['id'] }}"
                            {{ request()->get('tipo_produto_id') == $tipo_produto['id'] ? 'selected' : '' }}>
                            {{ $tipo_produto['nome'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:items-end">
                <x-limpar-filtro class="w-full sm:w-auto"/>

                <x-primary-button class="w-full sm:w-auto justify-center">
                    {{ __('Buscar') }}
                    <i class="fa-solid fa-magnifying-glass ml-2"></i>
                </x-primary-button>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th class="text-center">Código</th>
                    <th class="text-center">Status</th>
                    <th>Nome</th>
                    <th>Características</th>
                    <th>Estoque</th>
                    <th>Preço custo</th>
                    <th>Preço venda</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($produtos as $produto)
                    <tr>
                        <td class="w-24 text-center">
                            {{ $produto->id }}
                        </td>
                        <td class="w-24 text-center">
                            <x-status :status="$produto->status" :id="$produto->id" />
                        </td>
                        <td>
                            {{ $produto->nome }}
                        </td>
                        <td class="text-xs">
                            <p>Marca: {{ $produto['marca']->nome ?? '' }}</p>
                            <p>Tipo: {{ $produto['tipoProduto']->nome ?? '' }}</p>
                        </td>
                        <td>
                            {{ $produto->quantidade }}
                        </td>
                        <td>
                            {{ number_format($produto->preco_custo, 2, ',', '.') }}
                        </td>
                        <td>
                            {{ number_format($produto->preco_venda, 2, ',', '.') }}
                        </td>
                        <td class="w-24">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('produtos.show', $produto->id) }}" class="btn btn-info">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </a>
                                <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-warning">
                                    <i class="fa-solid fa-pen-to-square"></i> 
                                </a>
                                <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" class="form-delete">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class='btn btn-danger'>
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $produtos->links() }}
    </div>
</x-app-layout>
