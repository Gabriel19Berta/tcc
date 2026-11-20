@use('App\Relatorios\Recebimentos\MovimentacoesRecebimentosRelatorio')

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h2 class="font-semibold text-primary">{{ $relatorio->titulo() }}</h2>

        <form action="{{ route('relatorios.gerar') }}" method="GET">
            <input type="hidden" name="relatorio" value="{{ $relatorio->slug() }}">
            <input type="hidden" name="formato" value="excel">

            <div class="flex flex-wrap gap-3">
                <div>
                    <x-input-label for="data-inicio" value="Data inicial da movimentação"/>
                    <x-input id="data-inicio" type="date" name="data-inicio" :value="old('data-inicio')" />
                    <x-input-error :messages="$errors->get('data-inicio')" />
                </div>
                <div>
                    <x-input-label for="data-fim" value="Data final da movimentação"/>
                    <x-input id="data-fim" type="date" name="data-fim" :value="old('data-fim')" />
                    <x-input-error :messages="$errors->get('data-fim')" />
                </div>
                <div class="w-full sm:w-56">
                    <x-input-label for="cliente" value="Cliente" />
                    <x-select-pessoa name="cliente" tipo="cliente" />
                    <x-input-error :messages="$errors->get('cliente')" />
                </div>
                <div class="w-full sm:w-56">
                    <x-input-label for="forma_pagamento_id" value="Forma de pagamento" />
                    <select id="forma_pagamento_id" name="forma_pagamento_id" class="select2">
                        <option value=""></option>
                        @foreach ($relatorio->formasPagamento() as $forma)
                            <option value="{{ $forma->id }}" @selected((string) old('forma_pagamento_id') === (string) $forma->id)>
                                {{ $forma->nome }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('forma_pagamento_id')" />
                </div>
                <div>
                    <x-input-label for="tipo" value="Tipo de movimentação" />
                    <select id="tipo" name="tipo">
                        <option value="">Todos</option>
                        <option value="{{ MovimentacoesRecebimentosRelatorio::TIPO_PAGAMENTO }}"
                            @selected(old('tipo') === MovimentacoesRecebimentosRelatorio::TIPO_PAGAMENTO)>Pagamento</option>
                        <option value="{{ MovimentacoesRecebimentosRelatorio::TIPO_ESTORNO }}"
                            @selected(old('tipo') === MovimentacoesRecebimentosRelatorio::TIPO_ESTORNO)>Estorno</option>
                    </select>
                    <x-input-error :messages="$errors->get('tipo')" />
                </div>
            </div>

            <div class="float-right py-2">
                <x-primary-button data-download>
                    {{ __('Gerar') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
