<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1 class="inline text-primary font-semibold text-2xl leading-tight">
                {{ __('Clientes') }}
            </h1>
            <x-action-link href="{{ route('clientes.create') }}">
                Cadastrar
            </x-action-link>
        </div>
    </x-slot>

    <div class="py-4 mx-auto sm:px-6 lg:px-8 overflow-auto">
        
        {{-- FILTROS --}}
        <form method="GET" action="{{ route('clientes.index') }}" class="mb-4 flex justify-between items-end">
            <div class="flex justify-between items-center gap-2">
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
                    <x-text-input id="nome" name="nome" type="text" :value="request('nome')" class="w-96 placeholder:text-gray-400 placeholder:text-sm" placeholder="Digite o nome, cpf ou cnpj"/>
                </div>
                <div>
                    <x-input-label for="tipo" :value="__('Tipo')" />
                    <select id="tipo" name="tipo">
                        <option value="" {{ request('tipo') === null ? 'selected' : '' }}>Todos</option>
                        <option value="f" {{ request('tipo') === 'f' ? 'selected' : '' }}>Física</option>
                        <option value="j" {{ request('tipo') === 'j' ? 'selected' : '' }}>Jurídica</option>
                    </select>
                </div>
            </div>
            <div class="ml-1">
                <x-limpar-filtro/>
                <x-primary-button>
                    {{ __('Buscar') }}
                    <i class="fa-solid fa-magnifying-glass ml-2"></i>
                </x-primary-button>
            </div>            
        </form>

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Status</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Documento</th>
                    <th>Celular</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($clientes as $cliente)
                    <tr>
                        <td>
                            {{ $cliente->id }}
                        </td>
                        <td>
                            <x-status :status="$cliente->status" :id="$cliente->id" />
                        </td>
                        <td>
                            {{ $cliente->nome }}
                        </td>
                        @if ($cliente->cpf)
                            <td>
                                Física
                            </td>
                            <td>
                                {{ $cliente->cpf }}
                            </td>
                        @elseif ($cliente->cnpj)
                            <td>
                                Jurídica
                            </td>
                            <td>
                                {{ $cliente->cnpj }}
                            </td>
                        @else
                            <td></td>
                            <td></td>
                        @endif
                        <td>
                            {{ $cliente->celular }}
                        </td>
                        <td class="flex gap-2 justify-center">
                            <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-info">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </a>
                            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-warning">
                                <i class="fa-solid fa-pen-to-square"></i> 
                            </a>
                            <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="form-delete">
                                @csrf

                                <button type="submit" class='btn btn-danger'>
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $clientes->links() }}
    </div>
</x-app-layout>
