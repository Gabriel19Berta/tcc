@use('App\Support\Formatador')

@php
    $venda = $recebimento->venda;

    // Após erro de validação, o valor pode voltar convertido ("1234.56"); reformatamos para a máscara.
    $valorAntigo = old('valor');
    $valor = is_numeric($valorAntigo) ? Formatador::moeda((float) $valorAntigo) : $valorAntigo;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Receber Pagamento') }}
            </h1>
        </div>
    </x-slot>

    <div class="py-4">
        <form action="{{ route('recebimentos.pagamento.store', $recebimento->id) }}" method="POST">
            @csrf
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- CONTEXTO (somente consulta) --}}
                    <dl class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6 pb-6 border-b border-gray-200 text-sm">
                        <div>
                            <dt class="text-gray-500">Recebimento</dt>
                            <dd class="font-medium">#{{ $recebimento->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Venda</dt>
                            <dd class="font-medium">
                                <a href="{{ route('vendas.show', $recebimento->venda_id) }}" class="text-primary hover:underline">
                                    #{{ $recebimento->venda_id }}
                                </a>
                            </dd>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <dt class="text-gray-500">Cliente</dt>
                            <dd class="font-medium">{{ $venda->cliente?->pessoa?->nome ?? 'Consumidor não identificado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Vencimento</dt>
                            <dd class="font-medium">{{ Formatador::data($recebimento->data_vencimento, 'd/m/Y') }}</dd>
                        </div>
                        <div class="hidden md:block"></div>
                        <div>
                            <dt class="text-gray-500">Valor original</dt>
                            <dd class="font-medium">{{ Formatador::reais($recebimento->valor) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Total já recebido</dt>
                            <dd class="font-medium">{{ Formatador::reais($totalRecebido) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Saldo restante</dt>
                            <dd class="font-medium">{{ Formatador::reais($saldoRestante) }}</dd>
                        </div>
                    </dl>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="forma_pagamento_id" :value="__('Forma de pagamento utilizada')" class="required" />
                            <select name="forma_pagamento_id" id="forma_pagamento_id" class="select2">
                                <option value=""></option>
                                @foreach ($formasPagamento as $forma)
                                    <option value="{{ $forma->id }}"
                                        @selected((string) old('forma_pagamento_id', $recebimento->forma_pagamento_id) === (string) $forma->id)>
                                        {{ $forma->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('forma_pagamento_id')
                                <p class="mt-1 text-sm text-danger-500 input-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-1">
                            <x-input-label for="valor" :value="__('Valor recebido')" class="required" />
                            <x-input id="valor" name="valor" type="text" inputmode="decimal"
                                class="w-full mask-valor" data-mask-prefix="R$ " placeholder="R$ 0,00"
                                :value="$valor" />
                            <p class="mt-1 text-xs text-gray-500">
                                Máximo: {{ Formatador::reais($saldoRestante) }}
                            </p>
                        </div>

                        <div class="md:col-span-1">
                            <x-input-label for="data_pagamento" :value="__('Data do pagamento')" class="required" />
                            <x-input id="data_pagamento" name="data_pagamento" type="date" class="w-full"
                                :value="old('data_pagamento', now()->format('Y-m-d'))" />
                        </div>

                        <div class="md:col-span-1">
                            <x-input-label for="hora_pagamento" :value="__('Horário do pagamento')" class="required" />
                            <x-input id="hora_pagamento" name="hora_pagamento" type="time" step="60" class="w-full"
                                :value="old('hora_pagamento', now()->format('H:i'))" />
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-gray-500">
                        Informe a data e o horário em que o pagamento foi efetivamente recebido.
                        O recebimento fica quitado quando o saldo restante chegar a zero.
                    </p>
                </div>
            </div>

            <x-form-button :cancelUrl="route('recebimentos.index')" submitText="Confirmar recebimento" />
        </form>
    </div>
</x-app-layout>
