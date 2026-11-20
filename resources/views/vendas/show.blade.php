@use('App\Support\Formatador')

@php
    // Grupos exibidos na seção de recebimentos. Cancelados só aparecem se existirem.
    $gruposRecebimentos = [
        'pago' => ['titulo' => 'Recebimentos pagos', 'vazio' => 'Nenhum recebimento pago.'],
        'pendente' => ['titulo' => 'Recebimentos pendentes', 'vazio' => 'Nenhum recebimento pendente.'],
        'cancelado' => ['titulo' => 'Recebimentos cancelados', 'vazio' => null],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h1>
                {{ __('Venda') }} #{{ $venda->id }}
            </h1>

            <div class="flex gap-2">
                @if ($venda->status === \App\Models\Venda::STATUS_ABERTA)
                    <x-action-link href="{{ route('vendas.edit', $venda->id) }}" color="beige">
                        Abrir no PDV
                    </x-action-link>
                @endif

                <x-action-link href="{{ route('vendas.index') }}">
                    Voltar
                </x-action-link>
            </div>
        </div>
    </x-slot>

    <div class="py-4 space-y-6">

        {{-- INFORMAÇÕES GERAIS E RESUMO FINANCEIRO --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg overflow-hidden">
                <x-show-table>
                    <tr class="bg-gray-50">
                        <th colspan="2" scope="colgroup" class="text-base text-primary">
                            Informações gerais
                        </th>
                    </tr>
                    <tr>
                        <th>Código</th>
                        <td>{{ $venda->id }}</td>
                    </tr>
                    <tr>
                        <th>Data</th>
                        <td>{{ Formatador::data($venda->data, 'd/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <x-status-badge :status="$venda->status" />
                        </td>
                    </tr>
                    <tr>
                        <th>Cliente</th>
                        <td>{{ $venda->cliente?->pessoa?->nome ?? 'Venda sem cliente' }}</td>
                    </tr>
                    <tr>
                        <th>Funcionário</th>
                        <td>{{ $venda->funcionario?->pessoa?->nome ?? 'Não informado' }}</td>
                    </tr>
                    <tr>
                        <th>Última alteração</th>
                        <td>{{ Formatador::data($venda->updated_at, 'd/m/Y H:i') }}</td>
                    </tr>
                    @if ($venda->status === \App\Models\Venda::STATUS_CANCELADA || $venda->justificativa_cancelamento)
                        <tr>
                            <th>Justificativa do cancelamento</th>
                            <td class="whitespace-normal">{{ $venda->justificativa_cancelamento ?: 'Não informada' }}</td>
                        </tr>
                    @endif
                </x-show-table>
            </div>

            <div class="bg-white rounded-lg overflow-hidden">
                <x-show-table>
                    <tr class="bg-gray-50">
                        <th colspan="2" scope="colgroup" class="text-base text-primary">
                            Resumo financeiro
                        </th>
                    </tr>
                    <tr>
                        <th>Valor dos produtos</th>
                        <td>{{ Formatador::reais($venda->valor_produtos) }}</td>
                    </tr>
                    <tr>
                        <th>Descontos</th>
                        <td>- {{ Formatador::reais($venda->descontos) }}</td>
                    </tr>
                    <tr>
                        <th>Outros custos</th>
                        <td>+ {{ Formatador::reais($venda->outros_custos) }}</td>
                    </tr>
                    <tr>
                        <th>Valor total</th>
                        <td class="font-bold">{{ Formatador::reais($venda->valor_total) }}</td>
                    </tr>
                </x-show-table>
            </div>
        </div>

        {{-- PRODUTOS --}}
        <section>
            <h2 class="mb-2 text-lg font-semibold text-primary">Produtos</h2>

            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th class="text-right">Quantidade</th>
                            <th class="text-right">Preço unitário</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($venda->itens as $item)
                            <tr>
                                <td>{{ $item->produto?->nome ?? 'Produto #' . $item->produto_id }}</td>
                                <td class="text-right">{{ number_format((float) $item->quantidade, 2, ',', '.') }}</td>
                                <td class="text-right">{{ Formatador::reais($item->preco_unitario) }}</td>
                                <td class="text-right">
                                    {{ Formatador::reais(round((float) $item->quantidade * (float) $item->preco_unitario, 2)) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-gray-500">
                                    Nenhum produto registrado nesta venda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- RECEBIMENTOS (um registro por linha, separados por status) --}}
        @foreach ($gruposRecebimentos as $statusGrupo => $grupo)
            @php
                $registros = $recebimentos->get($statusGrupo, collect());
            @endphp

            {{-- Cancelados só são exibidos quando existirem. --}}
            @continue($grupo['vazio'] === null && $registros->isEmpty())

            <section>
                <h2 class="mb-2 text-lg font-semibold text-primary">{{ $grupo['titulo'] }}</h2>

                <div class="overflow-x-auto">
                    <table>
                        <thead>
                            <tr>
                                <th class="text-center">Código</th>
                                <th>Registrado em</th>
                                <th>Forma de pagamento</th>
                                <th>Descrição</th>
                                <th>Vencimento</th>
                                <th>Pagamento</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registros as $recebimento)
                                <tr>
                                    <td class="text-center">{{ $recebimento->id }}</td>
                                    <td>{{ Formatador::data($recebimento->created_at, 'd/m/Y H:i') }}</td>
                                    <td>{{ $recebimento->formaPagamento?->nome ?? 'Não informada' }}</td>
                                    <td>{{ $recebimento->descricao ?: '—' }}</td>
                                    <td>{{ Formatador::data($recebimento->data_vencimento, 'd/m/Y') }}</td>
                                    <td>{{ Formatador::data($recebimento->data_pagamento, 'd/m/Y') ?: '—' }}</td>
                                    <td class="text-center">
                                        <x-status-badge :status="$recebimento->status" type="recebimento" />
                                    </td>
                                    <td class="text-right">{{ Formatador::reais($recebimento->valor) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-gray-500">
                                        {{ $grupo['vazio'] }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if ($statusGrupo === 'pendente')
                            <tfoot>
                                <tr class="bg-gray-50 font-semibold">
                                    <td colspan="7">
                                        Total: {{ $totalPendentes['quantidade'] }}
                                        {{ $totalPendentes['quantidade'] === 1 ? 'recebimento pendente' : 'recebimentos pendentes' }}
                                    </td>
                                    <td class="text-right">{{ Formatador::reais($totalPendentes['valor']) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </section>
        @endforeach
    </div>
</x-app-layout>
