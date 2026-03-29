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
                            <th colspan="2" scope="colgroup" class="text-base text-primary">
                                Dados Básicos
                            </th>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $cliente->status_formatada }}</td>
                        </tr>
                        <tr>
                            <th>Nome</th>
                            <td>{{ $cliente->nome }}</td>
                        </tr>

                        @if($cliente->tipo == 'f')
                            <tr>
                                <th>Tipo</th>
                                <td>{{ __('Física') }}</td>
                            </tr>
                            <tr>
                                <th>CPF</th>
                                <td>{{ $cliente->cpf }}</td>
                            </tr>

                            <tr>
                                <th>RG</th>
                                <td>{{ $cliente->rg }}</td>
                            </tr>

                            <tr>
                                <th>Data Nascimento</th>
                                <td>{{ $cliente->data_nascimento_formatada }}</td>
                            </tr>
                        @elseif ($cliente->tipo == 'j')
                            <tr>
                                <th>Tipo</th>
                                <td>{{ __('Jurídica') }}</td>
                            </tr>
                            <tr>
                                <th>CNPJ</th>
                                <td>{{ $cliente->cnpj }}</td>
                            </tr>

                            <tr>
                                <th>Inscrição Estadual</th>
                                <td>{{ $cliente->ie }}</td>
                            </tr>
                        @else
                            <tr>
                                <th>Tipo</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>CPF</th>
                                <td>{{ $cliente->cpf }}</td>
                            </tr>

                            <tr>
                                <th>RG</th>
                                <td>{{ $cliente->rg }}</td>
                            </tr>

                            <tr>
                                <th>Data Nascimento</th>
                                <td>{{ $cliente->data_nascimento_formatada }}</td>
                            </tr>
                        @endif
                        <tr class="bg-gray-50">
                            <th colspan="2" scope="colgroup" class="text-base text-primary">
                                Contato
                            </th>
                        </tr>
                        <tr>
                            <th>Celular</th>
                            <td>{{ $cliente->celular }}</td>
                        </tr>
                        <tr>
                            <th>Telefone</th>
                            <td>{{ $cliente->telefone }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $cliente->email }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <th colspan="2" scope="colgroup" class="text-base text-primary">
                                Endereço
                            </th>
                        </tr>
                        <tr>
                            <th>CEP</th>
                            <td>{{ $cliente->cep }}</td>
                        </tr>
                        <tr>
                            <th>Logradouro</th>
                            <td>{{ $cliente->logradouro }}</td>
                        </tr>
                        <tr>
                            <th>Número</th>
                            <td>{{ $cliente->numero }}</td>
                        </tr>
                        <tr>
                            <th>Complemento</th>
                            <td>{{ $cliente->complemento }}</td>
                        </tr>
                        <tr>
                            <th>Bairro</th>
                            <td>{{ $cliente->bairro }}</td>
                        </tr>
                        <tr>
                            <th>Cidade</th>
                            <td>{{ $cliente->cidade }}</td>
                        </tr>
                        <tr>
                            <th>UF</th>
                            <td>{{ $cliente->uf }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <th colspan="2" scope="colgroup" class="text-base text-primary">
                                Dados adicionais
                            </th>
                        </tr>
                        <tr>
                            <th>Observações</th>
                            <td>{{ $cliente->cliente->observacoes }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>