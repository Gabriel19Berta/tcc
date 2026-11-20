@use('App\Support\Formatador')

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
            <x-input id="codigo" name="codigo" type="number" min="1"
                :value="request('codigo')" class="w-24"
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
            <x-input id="data_inicio" name="data_inicio" type="date" :value="request('data_inicio')" />
        </div>

        <div>
            <x-input-label for="data_fim" :value="__('Data final')" />
            <x-input id="data_fim" name="data_fim" type="date" :value="request('data_fim')" />
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
                    {{ Formatador::data($venda->data, 'd/m/Y H:i') }}
                </td>

                <td>
                    {{ $venda->cliente?->pessoa?->nome ?? 'Consumidor não identificado' }}
                </td>

                <td>
                    {{ $venda->funcionario?->pessoa?->nome ?? 'Não informado' }}
                </td>

                <td class="text-center">
                    <x-status-badge :status="$venda->status" />
                </td>

                <td class="text-right">
                    {{ Formatador::reais($venda->valor_total) }}
                </td>

                <td class="w-24">
                    <div class="flex gap-2 justify-center">
                        <a href="{{ route('vendas.show', $venda->id) }}" class="btn btn-info" title="Visualizar venda">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </a>
                        <a href="{{ route('vendas.edit', $venda->id) }}" class="btn btn-warning" title="Abrir venda no PDV">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>

                        {{-- MAIS AÇÕES --}}
                        <x-dropdown align="right" width="48" direction="up">
                            <x-slot name="trigger">
                                <button type="button" class="btn bg-gray-500 hover:bg-gray-600 active:bg-gray-600" title="Mais ações">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <form method="POST" action="{{ route('vendas.cancelar', $venda->id) }}"
                                    data-confirm="Tem certeza de que deseja cancelar a venda #{{ $venda->id }}? Os recebimentos pendentes serão cancelados e o estoque será ajustado. Esta operação não poderá ser desfeita.">
                                    @csrf

                                    <x-dropdown-link :href="route('vendas.cancelar', $venda->id)"
                                        onclick="event.preventDefault(); this.closest('form').requestSubmit();">
                                        <i class="fa-solid fa-ban me-1 text-danger-500"></i> Cancelar venda
                                    </x-dropdown-link>
                                </form>
                                <form method="POST" action="{{ route('vendas.destroy', $venda->id) }}"
                                    data-confirm="Tem certeza de que deseja excluir a venda #{{ $venda->id }}? Essa ação é definitiva e não poderá ser desfeita. A venda será removida do sistema, incluindo recebimentos.">
                                    @csrf
                                    @method('DELETE')

                                    <x-dropdown-link :href="route('vendas.destroy', $venda->id)"
                                        onclick="event.preventDefault(); this.closest('form').requestSubmit();">
                                        <i class="fa-solid fa-trash me-1 text-danger-500"></i> Excluir venda
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
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

