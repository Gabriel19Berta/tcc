<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1 class="text-primary font-semibold text-2xl">
                {{ __('Dados do Funcionário') }}
            </h1>

            <x-action-link href="{{ route('funcionarios.index') }}">
                Voltar
            </x-action-link>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg overflow-hidden">
                <x-show-table>
                    <tbody>
                        <tr class="bg-gray-50">
                            <th colspan="2" scope="colgroup" class="text-base text-primary">
                                Dados Básicos
                            </th>
                        </tr>
                        <tr>
                            <th>Código</th>
                            <td>{{ $funcionario->funcionario->id }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $funcionario->status_formatada }}</td>
                        </tr>
                        <tr>
                            <th>Nome</th>
                            <td>{{ $funcionario->nome }}</td>
                        </tr>

                        @if($funcionario->tipo == 'f')
                            <tr>
                                <th>Tipo</th>
                                <td>{{ __('Física') }}</td>
                            </tr>
                            <tr>
                                <th>CPF</th>
                                <td>{{ $funcionario->cpf }}</td>
                            </tr>

                            <tr>
                                <th>RG</th>
                                <td>{{ $funcionario->rg }}</td>
                            </tr>

                            <tr>
                                <th>Data Nascimento</th>
                                <td>{{ $funcionario->data_nascimento_formatada }}</td>
                            </tr>
                        @elseif ($funcionario->tipo == 'j')
                            <tr>
                                <th>Tipo</th>
                                <td>{{ __('Jurídica') }}</td>
                            </tr>
                            <tr>
                                <th>CNPJ</th>
                                <td>{{ $funcionario->cnpj }}</td>
                            </tr>

                            <tr>
                                <th>Inscrição Estadual</th>
                                <td>{{ $funcionario->ie }}</td>
                            </tr>
                        @else
                            <tr>
                                <th>Tipo</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>CPF</th>
                                <td>{{ $funcionario->cpf }}</td>
                            </tr>

                            <tr>
                                <th>RG</th>
                                <td>{{ $funcionario->rg }}</td>
                            </tr>

                            <tr>
                                <th>Data Nascimento</th>
                                <td>{{ $funcionario->data_nascimento_formatada }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Data Admissão</th>
                            @php
                            @endphp
                            <td>{{ $funcionario->funcionario->data_admissao_formatada }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <th colspan="2" scope="colgroup" class="text-base text-primary">
                                Contato
                            </th>
                        </tr>
                        <tr>
                            <th>Celular</th>
                            <td>{{ $funcionario->celular }}</td>
                        </tr>
                        <tr>
                            <th>Telefone</th>
                            <td>{{ $funcionario->telefone }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $funcionario->email }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <th colspan="2" scope="colgroup" class="text-base text-primary">
                                Endereço
                            </th>
                        </tr>
                        <tr>
                            <th>CEP</th>
                            <td>{{ $funcionario->cep }}</td>
                        </tr>
                        <tr>
                            <th>Logradouro</th>
                            <td>{{ $funcionario->logradouro }}</td>
                        </tr>
                        <tr>
                            <th>Número</th>
                            <td>{{ $funcionario->numero }}</td>
                        </tr>
                        <tr>
                            <th>Complemento</th>
                            <td>{{ $funcionario->complemento }}</td>
                        </tr>
                        <tr>
                            <th>Bairro</th>
                            <td>{{ $funcionario->bairro }}</td>
                        </tr>
                        <tr>
                            <th>Cidade</th>
                            <td>{{ $funcionario->cidade }}</td>
                        </tr>
                        <tr>
                            <th>UF</th>
                            <td>{{ $funcionario->uf }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <th colspan="2" scope="colgroup" class="text-base text-primary">
                                Dados adicionais
                            </th>
                        </tr>
                        <tr>
                            <th>Observações</th>
                            <td>{{ $funcionario->funcionario->observacoes }}</td>
                        </tr>
                    </tbody>
                </x-show-table>
            </div>
        </div>
    </div>
</x-app-layout>