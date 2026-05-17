<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Marcas') }}
            </h1>
            <x-action-link href="{{ route('marcas.create') }}">
                Cadastrar
            </x-action-link>
        </div>
    </x-slot>

    <div class="py-4 mx-auto sm:px-6 lg:px-8 overflow-auto">
        
        {{-- FILTROS --}}
        <form method="GET" action="{{ route('marcas.index') }}" class="mb-4 flex flex-col lg:flex-row lg:justify-between gap-4 sm:px-0 px-4">
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
                    <x-text-input id="nome" name="nome" type="text" :value="request('nome')" class="w-96" placeholder="Digite o nome, cpf ou cnpj"/>
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
                    <th>Código</th>
                    <th>Status</th>
                    <th>Nome</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($marcas as $marca)
                    <tr>
                        <td>
                            {{ $marca->id }}
                        </td>
                        <td>
                            <x-status :status="$marca->status" :id="$marca->id" />
                        </td>
                        <td>
                            {{ $marca->nome }}
                        </td>
                        <td class="flex gap-2 justify-center">
                            <a href="{{ route('marcas.show', $marca->id) }}" class="btn btn-info">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </a>
                            <a href="{{ route('marcas.edit', $marca->id) }}" class="btn btn-warning">
                                <i class="fa-solid fa-pen-to-square"></i> 
                            </a>
                            <form action="{{ route('marcas.destroy', $marca->id) }}" method="POST" class="form-delete">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class='btn btn-danger'>
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $marcas->links() }}
    </div>
</x-app-layout>
