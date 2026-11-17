
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1>
                {{ __('Vendas') }}
            </h1>

            <div class="flex gap-2">
                <x-action-link href="{{ route('vendas.create') }}">
                    Nova venda
                </x-action-link>
            </div>
        </div>
    </x-slot>

    {{-- FILTROS --}}
    <x-filter-form :action="route('vendas.index')">
        <div>
            <x-input-label for="codigo" :value="__('Código')" />
            <x-input
                id="codigo"
                name="codigo"
                type="number"
                min="1"
                :value="request('codigo')"
                class="w-24"
            />
        </div>

        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status">
                <option value="todos"
                    {{ request('status', 'todos') === 'todos' ? 'selected' : '' }}>
                    Todos
                </option>
                <option value="aberta"
                    {{ request('status') === 'aberta' ? 'selected' : '' }}>
                    Aberta
                </option>
                <option value="finalizada"
                    {{ request('status') === 'finalizada' ? 'selected' : '' }}>
                    Finalizada
                </option>
                <option value="quitada"
                    {{ request('status') === 'quitada' ? 'selected' : '' }}>
                    Quitada
                </option>
                <option value="cancelada"
                    {{ request('status') === 'cancelada' ? 'selected' : '' }}>
                    Cancelada
                </option>
            </select>
        </div>

        <div>
            <x-input-label for="data_inicio" :value="__('Data inicial')" />
            <x-input
                id="data_inicio"
                name="data_inicio"
                type="date"
                :value="request('data_inicio')"
            />
        </div>

        <div>
            <x-input-label for="data_fim" :value="__('Data final')" />
            <x-input
                id="data_fim"
                name="data_fim"
                type="date"
                :value="request('data_fim')"
            />
        </div>
    </x-filter-form>

    <table>
        <thead>
        <tr>
            <th class="text-center">Código</th>
            <th>Data</th>
            <th>Cliente</th>
            <th>Funcionário</th>
            <th class="text-center">Status</th>
            <th class="text-right">Valor total</th>
            <th class="text-center">Ações</th>
        </tr>
        </thead>

        <tbody>
        @forelse ($vendas as $venda)
            <tr>
                <td class="w-24 text-center">
                    {{ $venda->id }}
                </td>

                <td>
                    {{ $venda->data?->format('d/m/Y H:i') }}
                </td>

                <td>
                    {{ $venda->cliente?->pessoa?->nome ?? 'Consumidor não identificado' }}
                </td>

                <td>
                    {{ $venda->funcionario?->pessoa?->nome ?? 'Não informado' }}
                </td>

                <td class="text-center">
                    @switch($venda->status)
                        @case('aberta')
                            <span class="">Aberta</span>
                            @break

                        @case('finalizada')
                            <span class="">Finalizada</span>
                            @break

                        @case('quitada')
                            <span class="">Quitada</span>
                            @break

                        @case('cancelada')
                            <span class="">Cancelada</span>
                            @break

                        @default
                            {{ $venda->status }}
                    @endswitch
                </td>

                <td class="text-right">
                    R$ {{ number_format($venda->valor_total, 2, ',', '.') }}
                </td>

                <td class="w-24">
                    <div class="flex gap-2 justify-center">
                        <a
                            href="{{ route('vendas.edit', $venda->id) }}"
                            class="btn btn-info"
                            title="Abrir venda no PDV"
                        >
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">
                    Nenhuma venda encontrada.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $vendas->withQueryString()->links() }}
</x-app-layout>

