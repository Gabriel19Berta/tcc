@use('App\Support\Formatador')

@php
    // Após erro de validação, o valor pode voltar convertido ("1234.56"); reformatamos para a máscara.
    $valorAntigo = old('valor');
    $valor = is_numeric($valorAntigo) ? Formatador::moeda((float) $valorAntigo) : $valorAntigo;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Lançar Recebimento') }}
            </h1>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                {{-- CONTEXTO (somente consulta) --}}
                <dl class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Venda</dt>
                        <dd class="font-medium">
                            <a href="{{ route('vendas.show', $venda->id) }}" class="text-primary hover:underline">
                                #{{ $venda->id }}
                            </a>
                        </dd>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <dt class="text-gray-500">Cliente</dt>
                        <dd class="font-medium">{{ $venda->cliente?->pessoa?->nome ?? 'Consumidor não identificado' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Total da venda</dt>
                        <dd class="font-medium">{{ Formatador::reais($venda->valor_total) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Já lançado (não cancelado)</dt>
                        <dd class="font-medium">{{ Formatador::reais($totalLancado) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Saldo disponível</dt>
                        <dd class="font-medium">{{ Formatador::reais($saldoDisponivel) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- A tela só é exibida quando há saldo (validado no controller). --}}
        <form action="{{ route('recebimentos.store', $venda->id) }}" method="POST">
            @csrf
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-2">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="forma_pagamento_id" :value="__('Forma de pagamento')" class="required" />
                            <select name="forma_pagamento_id" id="forma_pagamento_id" class="select2">
                                <option value=""></option>
                                @foreach ($formasPagamento as $forma)
                                    <option value="{{ $forma->id }}"
                                        @selected((string) old('forma_pagamento_id') === (string) $forma->id)>
                                        {{ $forma->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('forma_pagamento_id')
                                <p class="mt-1 text-sm text-danger-500 input-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-1">
                            <x-input-label for="valor" :value="__('Valor')" class="required" />
                            <x-input id="valor" name="valor" type="text" inputmode="decimal"
                                class="w-full mask-valor" data-mask-prefix="R$ " placeholder="R$ 0,00"
                                :value="$valor" />
                            <p class="mt-1 text-xs text-gray-500">
                                Máximo: {{ Formatador::reais($saldoDisponivel) }}
                            </p>
                        </div>

                        <div class="md:col-span-1">
                            <x-input-label for="data_vencimento" :value="__('Data de vencimento')" class="required" />
                            <x-input id="data_vencimento" name="data_vencimento" type="date" class="w-full"
                                :value="old('data_vencimento')" />
                        </div>

                        <div class="md:col-span-1">
                            <x-input-label for="status" :value="__('Status')" class="required" />
                            <select id="status" name="status" class="w-full">
                                @foreach ([\App\Models\Recebimento::STATUS_PENDENTE, \App\Models\Recebimento::STATUS_PAGO] as $status)
                                    <option value="{{ $status }}" @selected(old('status', \App\Models\Recebimento::STATUS_PENDENTE) === $status)>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-danger-500 input-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <x-form-button :cancelUrl="route('vendas.index')" submitText="Lançar" />
        </form>
    </div>
</x-app-layout>
