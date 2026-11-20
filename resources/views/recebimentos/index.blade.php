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
            <x-input id="data_fim" name="data_fim" type="date" :value="old('data_fim', request('data_fim'))" />
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

                {{-- Ações: serão adicionadas nas próximas etapas do CRUD de recebimentos. --}}
                <td class="w-24"></td>
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
