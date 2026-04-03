<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Funcionários') }}
            </h1>
            <x-action-link href="{{ route('funcionarios.create') }}">
                Cadastrar
            </x-action-link>
        </div>
    </x-slot>

    <div class="py-4 mx-auto sm:px-6 lg:px-8 overflow-auto">
        
        {{-- FILTROS --}}
        <form method="GET" action="{{ route('funcionarios.index') }}" class="mb-4 flex flex-col lg:flex-row lg:justify-between gap-4 sm:px-0 px-4">
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
                    <th>Tipo</th>
                    <th>Documento</th>
                    <th>Celular</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($funcionarios as $funcionario)
                    <tr>
                        <td>
                            {{ $funcionario->funcionario->id }}
                        </td>
                        <td>
                            <x-status :status="$funcionario->status" :id="$funcionario->id" />
                        </td>
                        <td>
                            {{ $funcionario->nome }}
                        </td>
                        @if ($funcionario->cpf)
                            <td>
                                Física
                            </td>
                            <td>
                                {{ $funcionario->cpf }}
                            </td>
                        @elseif ($funcionario->cnpj)
                            <td>
                                Jurídica
                            </td>
                            <td>
                                {{ $funcionario->cnpj }}
                            </td>
                        @else
                            <td></td>
                            <td></td>
                        @endif
                        <td>
                            {{ $funcionario->celular }}
                        </td>
                        <td class="flex gap-2 justify-center">
                            <a href="{{ route('funcionarios.show', $funcionario->id) }}" class="btn btn-info">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </a>
                            <a href="{{ route('funcionarios.edit', $funcionario->id) }}" class="btn btn-warning">
                                <i class="fa-solid fa-pen-to-square"></i> 
                            </a>
                            <form action="{{ route('funcionarios.destroy', $funcionario->funcionario->id) }}" method="POST" class="form-delete">
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
        {{ $funcionarios->links() }}
    </div>

</x-app-layout>
