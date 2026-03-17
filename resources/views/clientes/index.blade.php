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
        <form method="GET" action="{{ route('clientes.index') }}" class="mb-4 flex justify-between items-center">
            <div class="flex justify-between items-center gap-4">
                <div>
                    <x-input-label for="codigo" :value="__('Código')" />
                    <x-text-input id="codigo" name="codigo" type="number" min="0" :value="request('codigo')" />
                </div>
                <div>
                    <x-input-label for="nome" :value="__('Nome')" />
                    <x-text-input id="nome" name="nome" type="text" :value="request('nome')" />
                </div>
            </div>
            <div>
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
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($clientes as $cliente)
                    <tr>
                        <td>
                            {{ $cliente->id }}
                        </td>
                        <td>
                            {{ $cliente->status }}
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
                            <td>-</td>
                            <td></td>
                        @endif
                        <td>
                            {{ $cliente->celular }}
                        </td>
                        <td>
                            <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST">
                                @csrf

                                <button type="submit" class='btn-excluir'>
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
