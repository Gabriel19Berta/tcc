<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Clientes') }}
            </h1>
            <x-action-link href="{{ route('clientes.create') }}">
                Cadastrar
            </x-action-link>
        </div>
    </x-slot>
 
    {{-- FILTROS --}}
    <x-filter-form :action="route('clientes.index')">
        <div>
            <x-input-label for="codigo" :value="__('Código')" />
            <x-input id="codigo" name="codigo" type="number" min="0" :value="request('codigo')" class="w-24" />
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
            <x-input id="nome" name="nome" type="text" :value="request('nome')" class="w-96" placeholder="Digite o nome, cpf ou cnpj"/>
        </div>
        <div>
            <x-input-label for="tipo" :value="__('Tipo')" />
            <select id="tipo" name="tipo">
                <option value="" {{ request('tipo') === null ? 'selected' : '' }}>Todos</option>
                <option value="f" {{ request('tipo') === 'f' ? 'selected' : '' }}>Física</option>
                <option value="j" {{ request('tipo') === 'j' ? 'selected' : '' }}>Jurídica</option>
            </select>
        </div>
    </x-filter-form>

    <table>
        <thead>
            <tr>
                <th class="text-center">Código</th>
                <th class="text-center">Status</th>
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
                    <td class="w-24 text-center">
                        {{ $cliente->cliente->id }}
                    </td>
                    <td class="w-24 text-center">
                        <x-status :status="$cliente->status" :id="$cliente->id" model="pessoas"/>
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
                    <td class="w-24">
                        <div class="flex gap-2 justify-center">
                            <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-info">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </a>
                            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-warning">
                                <i class="fa-solid fa-pen-to-square"></i> 
                            </a>
                            <form action="{{ route('clientes.destroy', $cliente->cliente->id) }}" method="POST" class="form-delete">
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
    {{ $clientes->withQueryString()->links() }}
</x-app-layout>
