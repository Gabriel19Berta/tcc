@use('App\Support\Formatador')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1>
                {{ __('Recebimentos') }}
            </h1>
        </div>
    </x-slot>

    {{-- FILTROS --}}
    <x-filter-form :action="route('recebimentos.index')">
        <div>
            <x-input-label for="codigo" :value="__('Código')" />
            <x-input id="codigo" name="codigo" type="number" min="1"
                :value="old('codigo', request('codigo'))" class="w-24"
            />
        </div>

        <div>
            <x-input-label for="venda" :value="__('Venda')" />
            <x-input id="venda" name="venda" type="number" min="1"
                :value="old('venda', request('venda'))" class="w-24"
            />
        </div>

        <div class="w-full sm:w-56">
            <x-input-label for="cliente" :value="__('Cliente')" />
            <x-select-pessoa name="cliente" tipo="cliente" />
        </div>

        <div class="w-full sm:w-56">
            <x-input-label for="forma_pagamento_id" :value="__('Forma de pagamento')" />
            <select id="forma_pagamento_id" name="forma_pagamento_id" class="select2">
                <option value=""></option>
                @foreach ($formasPagamento as $forma)
                    <option value="{{ $forma->id }}" @selected((string) request('forma_pagamento_id') === (string) $forma->id)>
                        {{ $forma->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status">
                <option value="todos" @selected(request('status', 'todos') === 'todos')>Todos</option>
                @foreach (\App\Models\Recebimento::STATUS as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <x-input-label for="data_inicio" :value="__('Vencimento inicial')" />
            <x-input id="data_inicio" name="data_inicio" type="date" :value="old('data_inicio', request('data_inicio'))" />
        </div>

        <div>
            <x-input-label for="data_fim" :value="__('Vencimento final')" />
            <x-input id="data_fim" name="data_fim" type="date" data-data-inicial="data_inicio" :value="old('data_fim', request('data_fim'))" />
        </div>
    </x-filter-form>

    <table>
        <thead>
            <tr>
                <th class="text-center">Código</th>
                <th class="text-center">Venda</th>
                <th>Cliente</th>
                <th>Forma de pagamento</th>
                <th>Vencimento</th>
                <th class="text-center">Status</th>
                <th class="text-right">Valor</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>

        <tbody>
        @forelse ($recebimentos as $recebimento)
            <tr>
                <td class="w-24 text-center">
                    {{ $recebimento->id }}
                </td>

                <td class="w-24 text-center">
                    <a href="{{ route('vendas.show', $recebimento->venda_id) }}" class="text-primary hover:underline">
                        #{{ $recebimento->venda_id }}
                    </a>
                </td>

                <td>
                    {{ $recebimento->venda?->cliente?->pessoa?->nome ?? 'Consumidor não identificado' }}
                </td>

                <td>
                    {{ $recebimento->formaPagamento?->nome ?? 'Não informada' }}
                </td>

                <td>
                    {{ Formatador::data($recebimento->data_vencimento, 'd/m/Y') }}
                </td>

                <td class="text-center">
                    <x-status-badge :status="$recebimento->status" type="recebimento" />
                </td>

                <td class="text-right">
                    {{ Formatador::reais($recebimento->valor) }}
                </td>

                <td class="w-24">
                    <div class="flex gap-2 justify-center">
                        @if ($recebimento->status === \App\Models\Recebimento::STATUS_PENDENTE)
                            <a href="{{ route('recebimentos.receber', $recebimento->id) }}"
                                class="btn bg-primary hover:bg-primary-600 active:bg-primary-600" title="Receber pagamento">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                            </a>
                        @else
                            {{-- Mantém o alinhamento das demais ações. --}}
                            <span class="btn invisible" aria-hidden="true">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                            </span>
                        @endif

                        <a href="{{ route('recebimentos.show', $recebimento->id) }}" class="btn btn-info" title="Visualizar recebimento">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </a>
                        <a href="{{ route('recebimentos.edit', $recebimento->id) }}" class="btn btn-warning" title="Editar recebimento">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <x-dropdown align="right" width="48" direction="up">
                            <x-slot name="trigger">
                                <button type="button" class="btn bg-gray-500 hover:bg-gray-600 active:bg-gray-600" title="Mais ações">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <form method="POST" action="{{ route('recebimentos.estornar', $recebimento->id) }}"
                                    data-confirm="Tem certeza de que deseja estornar o recebimento #{{ $recebimento->id }}? Todo o valor efetivamente recebido ({{ Formatador::reais($recebimento->valor_recebido ?? 0) }}) será estornado. O recebimento voltará a ficar pendente e poderá receber novos pagamentos. Os registros dos pagamentos anteriores serão preservados no histórico.">
                                    @csrf

                                    <x-dropdown-link :href="route('recebimentos.estornar', $recebimento->id)"
                                        onclick="event.preventDefault(); this.closest('form').requestSubmit();">
                                        <i class="fa-solid fa-rotate-left me-1 text-danger-500"></i> Estornar recebimento
                                    </x-dropdown-link>
                                </form>

                                <form method="POST" action="{{ route('recebimentos.cancelar', $recebimento->id) }}"
                                    data-confirm="Tem certeza de que deseja cancelar o recebimento #{{ $recebimento->id }}? Após o cancelamento, não será mais possível registrar novos pagamentos ou editar este recebimento. O histórico financeiro será preservado.">
                                    @csrf

                                    <x-dropdown-link :href="route('recebimentos.cancelar', $recebimento->id)"
                                        onclick="event.preventDefault(); this.closest('form').requestSubmit();">
                                        <i class="fa-solid fa-ban me-1 text-danger-500"></i> Cancelar recebimento
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center">
                    Nenhum recebimento encontrado.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $recebimentos->withQueryString()->links() }}
</x-app-layout>
