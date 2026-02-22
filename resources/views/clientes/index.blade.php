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

    <div class="py-8 mx-auto sm:px-6 lg:px-8">
        <table>
            <thead class="bg-gray-50">
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
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
