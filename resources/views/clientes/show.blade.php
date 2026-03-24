<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1 class="text-primary font-semibold text-2xl">
                {{ __('Dados do Cliente') }}
            </h1>

            <x-action-link href="{{ route('clientes.index') }}">
                Voltar
            </x-action-link>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg overflow-hidden">
                <table>
                    <tbody>
                        <tr class="bg-gray-50">
                            <td colspan="2" class="text-base px-4 py-2 font-semibold text-primary">
                                Dados Básicos
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium w-1/5">Nome</td>
                            <td class="px-4 py-2">{{ $cliente->nome }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">Tipo</td>
                            <td class="px-4 py-2">
                                {{ $cliente->tipo == 'f' ? 'Física' : 'Jurídica' }}
                            </td>
                        </tr>

                        @if($cliente->tipo == 'f')
                            <tr>
                                <td class="px-4 py-2 font-medium">CPF</td>
                                <td class="px-4 py-2">{{ $cliente->cpf }}</td>
                            </tr>

                            <tr>
                                <td class="px-4 py-2 font-medium">RG</td>
                                <td class="px-4 py-2">{{ $cliente->rg }}</td>
                            </tr>

                            <tr>
                                <td class="px-4 py-2 font-medium">Data Nascimento</td>
                                <td class="px-4 py-2">{{ $cliente->data_nascimento }}</td>
                            </tr>
                        @else
                            <tr>
                                <td class="px-4 py-2 font-medium">CNPJ</td>
                                <td class="px-4 py-2">{{ $cliente->cnpj }}</td>
                            </tr>

                            <tr>
                                <td class="px-4 py-2 font-medium">Inscrição Estadual</td>
                                <td class="px-4 py-2">{{ $cliente->ie }}</td>
                            </tr>
                        @endif

                        <!-- Contato -->
                        <tr class="bg-gray-50">
                            <td colspan="2" class="text-base px-4 py-2 font-semibold text-primary">
                                Contato
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 font-medium">Celular</td>
                            <td class="px-4 py-2">{{ $cliente->celular }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">Telefone</td>
                            <td class="px-4 py-2">{{ $cliente->telefone }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">Email</td>
                            <td class="px-4 py-2">{{ $cliente->email }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td colspan="2" class="text-base px-4 py-2 font-semibold text-primary">
                                Endereço
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">CEP</td>
                            <td class="px-4 py-2">{{ $cliente->cep }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">Logradouro</td>
                            <td class="px-4 py-2">{{ $cliente->logradouro }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">Número</td>
                            <td class="px-4 py-2">{{ $cliente->numero }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">Complemento</td>
                            <td class="px-4 py-2">{{ $cliente->complemento }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">Bairro</td>
                            <td class="px-4 py-2">{{ $cliente->bairro }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">Cidade</td>
                            <td class="px-4 py-2">{{ $cliente->cidade }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">UF</td>
                            <td class="px-4 py-2">{{ $cliente->uf }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td colspan="2" class="text-base px-4 py-2 font-semibold text-primary">
                                Dados adicionais
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">Observações</td>
                            <td class="px-4 py-2">{{ $cliente->cliente->observacoes }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>