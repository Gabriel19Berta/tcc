@use('App\Support\Formatador')

@php
    $venda = $recebimento->venda;
    $pessoa = $venda?->cliente?->pessoa;

    // Volta para a listagem com os filtros usados (ex.: recebimentos de uma venda).
    $anterior = url()->previous();
    $voltar = strtok($anterior, '?') === route('recebimentos.index') ? $anterior : route('recebimentos.index');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Dados do Recebimento') }}
            </h1>

            <x-action-link href="{{ $voltar }}">
                Voltar
            </x-action-link>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="bg-white rounded-lg overflow-hidden">
            <x-show-table>
                <tr class="bg-gray-50">
                    <th colspan="2" scope="colgroup" class="text-base text-primary">
                        Dados Básicos
                    </th>
                </tr>
                <tr>
                    <th>Código</th>
                    <td>{{ $recebimento->id }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><x-status-badge :status="$recebimento->status" type="recebimento" /></td>
                </tr>
                <tr>
                    <th>Descrição</th>
                    <td>{{ $recebimento->descricao }}</td>
                </tr>
                <tr>
                    <th>Forma de pagamento</th>
                    <td>{{ $recebimento->formaPagamento?->nome ?? 'Não informada' }}</td>
                </tr>
                <tr>
                    <th>Valor</th>
                    <td>{{ Formatador::reais($recebimento->valor) }}</td>
                </tr>
                <tr>
                    <th>Vencimento</th>
                    <td>{{ Formatador::data($recebimento->data_vencimento, 'd/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Criado em</th>
                    <td>{{ Formatador::data($recebimento->created_at) }}</td>
                </tr>
                <tr>
                    <th>Modificado em</th>
                    <td>{{ Formatador::data($recebimento->updated_at) }}</td>
                </tr>

                <tr class="bg-gray-50">
                    <th colspan="2" scope="colgroup" class="text-base text-primary">
                        Venda
                    </th>
                </tr>
                <tr>
                    <th>Código</th>
                    <td>
                        <a href="{{ route('vendas.show', $recebimento->venda_id) }}" class="text-primary hover:underline">
                            #{{ $recebimento->venda_id }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><x-status-badge :status="$venda?->status" /></td>
                </tr>
                <tr>
                    <th>Data</th>
                    <td>{{ Formatador::data($venda?->data, 'd/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Valor total</th>
                    <td>{{ Formatador::reais($venda?->valor_total) }}</td>
                </tr>

                <tr class="bg-gray-50">
                    <th colspan="2" scope="colgroup" class="text-base text-primary">
                        Cliente
                    </th>
                </tr>
                @if ($pessoa)
                    <tr>
                        <th>Nome</th>
                        <td>{{ $pessoa->nome }}</td>
                    </tr>
                    @if ($pessoa->tipo === 'j')
                        <tr>
                            <th>CNPJ</th>
                            <td>{{ Formatador::cnpj($pessoa->cnpj) }}</td>
                        </tr>
                    @else
                        <tr>
                            <th>CPF</th>
                            <td>{{ Formatador::cpf($pessoa->cpf) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>Celular</th>
                        <td>{{ Formatador::celular($pessoa->celular) }}</td>
                    </tr>
                    <tr>
                        <th>Telefone</th>
                        <td>{{ Formatador::telefone($pessoa->telefone) }}</td>
                    </tr>
                @else
                    <tr>
                        <th>Nome</th>
                        <td>Consumidor não identificado</td>
                    </tr>
                @endif
                {{-- Cada pagamento (parcial ou total) é uma movimentação separada. --}}
                <tr class="bg-gray-50">
                    <th colspan="2" scope="colgroup" class="text-base text-primary">
                        Histórico de pagamentos
                    </th>
                </tr>
                <tr>
                    <th>Valor original</th>
                    <td>{{ Formatador::reais($recebimento->valor) }}</td>
                </tr>
                <tr>
                    <th>Total recebido</th>
                    <td>{{ Formatador::reais($recebimento->totalRecebidoEmCentavos() / 100) }}</td>
                </tr>
                <tr>
                    <th>Saldo restante</th>
                    <td>{{ Formatador::reais(max(0, $recebimento->saldoEmCentavos()) / 100) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="p-0">
                        <table>
                            <thead>
                                <tr>
                                    <th>Data e horário</th>
                                    <th>Forma de pagamento</th>
                                    <th class="text-right">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recebimento->pagamentos as $pagamento)
                                    <tr>
                                        <td>{{ Formatador::data($pagamento->data_pagamento, 'd/m/Y H:i') }}</td>
                                        <td>{{ $pagamento->formaPagamento?->nome ?? 'Não informada' }}</td>
                                        <td class="text-right">{{ Formatador::reais($pagamento->valor) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-gray-500">
                                            Nenhum pagamento registrado para este recebimento.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </td>
                </tr>
            </x-show-table>
        </div>
    </div>
</x-app-layout>
