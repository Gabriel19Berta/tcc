<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div id="loader" class="fixed inset-0 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center z-[9999]">
        <div class="w-12 h-12 border-4 border-gray-300 border-t-primary rounded-full animate-spin mb-4"></div>
        <p class="text-gray-700 font-medium">Carregando...</p>
    </div>
    <div class="min-h-screen bg-gray-100">
        <div class="flex h-screen flex-col bg-white text-gray-700">

            {{-- CABEÇALHO --}}
            <header class="flex h-[68px] shrink-0 items-center justify-between bg-primary px-6 text-white">
                <h1 class="text-2xl font-bold text-white">
                    PDV @isset($venda) {{ ' - ' . $venda->id }} @endisset
                </h1>

                <div class="flex items-center gap-4">
                    <form method="GET" action="{{ route('vendas.index') }}">
                        <button type="submit" class="text-sm font-semibold hover:underline">
                            Consultar vendas
                        </button>
                    </form>

                    <div>
                        <select id="venda_aberta" class="rounded-lg border-gray-300 text-sm"
                            onchange="if (this.value) window.location.href = this.value">
                            <option value="">Continuar venda...</option>

                            @foreach ($vendasAbertas as $vendaAberta)
                                <option value="{{ route('vendas.edit', $vendaAberta->id) }}">
                                    Venda #{{ $vendaAberta->id }}
                                    - R$ {{ number_format($vendaAberta->valor_total, 2, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </header>

            {{-- CONTEÚDO PRINCIPAL --}}
            <main class="flex min-h-0 flex-1 overflow-hidden">

                {{-- PAINEL ESQUERDO --}}
                <section class="flex min-h-0 w-1/2 flex-col overflow-y-auto border-r border-gray-300 p-6">

                    {{-- MENSAGENS DE ERRO --}}
                    @if ($errors->has('venda'))
                        <div class="mb-4">
                            <x-alert type="error">
                                {{ $errors->first('venda') }}
                            </x-alert>
                        </div>
                    @endif

                    {{-- NAVEGAÇÃO --}}
                    <nav class="mb-2 flex shrink-0 overflow-hidden rounded-full border border-primary">
                        <button type="button" data-aba="produto" class="flex-1 rounded-full bg-primary px-4 py-3 font-semibold text-white"
                            aria-selected="true">
                            Produto
                        </button>

                        <button type="button" data-aba="cliente" class="flex-1 px-4 py-3 font-semibold" aria-selected="false">
                            Cliente
                        </button>

                        <button type="button" data-aba="pagamento" class="flex-1 px-4 py-3 font-semibold" aria-selected="false">
                            Pagamento
                        </button>
                    </nav>

                    {{-- CONTEÚDO DA ABA --}}
                    {{-- ABA PRODUTO --}}
                    <div id="aba-produto" class="flex-1">
                        <h2 class="mb-8 text-lg font-semibold text-primary">
                            Adicionar produto
                        </h2>

                        <form action="{{ route('vendas.itens.store') }}" method="POST">
                            @csrf

                            {{-- IDENTIFICAÇÃO DA VENDA --}}
                            @isset($venda)
                                <input type="hidden" name="venda_id" value="{{ $venda->id }}">
                            @endisset

                            {{-- PRODUTO --}}
                            <div class="mb-5">
                                <x-input-label for="produto_id" :value=" __('Produto')" />

                                <select name="produto_id" id="produto_id" class="select2 w-full">
                                    <option value="">Selecione um produto</option>

                                    @foreach ($produtos as $produto)
                                        <option value="{{ $produto->id }}" data-preco="{{ $produto->preco_venda }}"
                                            {{ old('produto_id') == $produto->id ? 'selected' : '' }}>
                                            {{ $produto->nome }} - R$ {{ number_format($produto->preco_venda, 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6">

                                <div>
                                    <x-input-label for="quantidade" :value="__('Quantidade')" />

                                    <x-input id="quantidade" name="quantidade" type="number" min="0.01"
                                        step="0.01" class="w-full" :value="old('quantidade', 1)"
                                    />

                                    @error('produto_id')
                                        <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <x-input-label for="preco_produto" :value="__('Preço')" />

                                    <x-input id="preco_produto" name="preco_produto" type="text"
                                        class="w-full mask-valor" placeholder="R$ 0,00" min="0"
                                        :value="old('preco_produto')"
                                    />
                                </div>

                            </div>

                            {{-- BOTÃO --}}
                            <div class="w-full text-center">
                                <button type="submit" class="w-full block text-center btn bg-primary hover:bg-primary-600 active:bg-primary-700">
                                    Adicionar produto
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ABA CLIENTE --}}
                    <div id="aba-cliente" class="flex-1 hidden">
                        <h2 class="mb-6 text-lg font-semibold text-primary">Cliente</h2>

                        @if(isset($venda))
                            <form id="form-cliente" action="{{ route('vendas.cliente.update', $venda->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-6">
                                    <x-input-label for="cliente_id" value="Cliente" />

                                    <select name="cliente_id" id="cliente_id" class="select2 w-full">
                                        <option value="">Venda sem cliente</option>

                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}"
                                                {{ old('cliente_id', $venda->cliente_id) == $cliente->id ? 'selected' : '' }}
                                            >
                                                {{ $cliente->pessoa->nome }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('cliente_id')
                                    <p class="mt-1 text-sm text-danger-500">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </form>

                            {{-- DADOS DO CLIENTE --}}
                            @if($venda->cliente && $venda->cliente->pessoa)
                                @php
                                    $pessoa = $venda->cliente->pessoa;
                                @endphp

                                <div class="rounded-lg border border-gray-300 p-4">
                                    <h3 class="mb-4 font-semibold text-text">
                                        Dados do cliente
                                    </h3>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">

                                        {{-- NOME --}}
                                        <div class="min-w-0">
                                            <span class="text-xs text-text-light">Nome</span>
                                            <p class="text-sm text-text break-words">
                                                {{ $pessoa->nome }}
                                            </p>
                                        </div>

                                        {{-- CPF / CNPJ --}}
                                        <div class="min-w-0">
                                            <span class="text-xs text-text-light">
                                                {{ $pessoa->tipo == 'j' ? 'CNPJ' : 'CPF' }}
                                            </span>

                                            <p class="text-sm text-text break-words">
                                                @if($pessoa->tipo == 'f')
                                                    {{ $pessoa->cpf ?? 'Não informado' }}
                                                @elseif($pessoa->tipo == 'j')
                                                    {{ $pessoa->cnpj ?? 'Não informado' }}
                                                @else
                                                    Não informado
                                                @endif
                                            </p>
                                        </div>

                                        {{-- TELEFONE --}}
                                        <div class="min-w-0">
                                            <span class="text-xs text-text-light">Telefone</span>
                                            <p class="text-sm text-text break-words">
                                                {{ $pessoa->telefone ?? 'Não informado' }}
                                            </p>
                                        </div>

                                        {{-- CELULAR --}}
                                        <div class="min-w-0">
                                            <span class="text-xs text-text-light">Celular</span>
                                            <p class="text-sm text-text break-words">
                                                {{ $pessoa->celular ?? 'Não informado' }}
                                            </p>
                                        </div>

                                        {{-- E-MAIL --}}
                                        <div class="min-w-0 sm:col-span-2">
                                            <span class="text-xs text-text-light">E-mail</span>
                                            <p class="text-sm text-text break-words">
                                                {{ $pessoa->email ?? 'Não informado' }}
                                            </p>
                                        </div>

                                        {{-- ENDEREÇO --}}
                                        <div class="min-w-0 sm:col-span-2">
                                            <span class="text-xs text-text-light">Endereço</span>

                                            <p class="text-sm text-text break-words">
                                                @if($pessoa->logradouro)
                                                    {{ $pessoa->logradouro }}

                                                    @if($pessoa->bairro)
                                                        - {{ $pessoa->bairro }}
                                                    @endif
                                                @else
                                                    Não informado
                                                @endif

                                                @if($pessoa->cidade || $pessoa->uf)
                                                    <span class="block">
                                                        {{ $pessoa->cidade }}{{ $pessoa->uf ? ' / ' . $pessoa->uf : '' }}
                                                    </span>
                                                @endif
                                            </p>
                                        </div>

                                    </div>
                                </div>

                            @else
                                <p class="text-sm text-text-light">
                                    Nenhum cliente selecionado para esta venda.
                                </p>
                            @endif
                        @else
                            <p class="text-sm text-text-light">Adicione um produto para iniciar uma venda e selecionar um cliente.</p>
                        @endif
                    </div>

                    {{-- ABA PAGAMENTO --}}
                    <div id="aba-pagamento" class="flex-1 hidden">

                        <h2 class="mb-5 text-lg font-semibold text-primary">
                            Pagamento
                        </h2>

                        @if(isset($venda))

                            {{-- VALORES DA VENDA --}}
                            <form id="form-valores" action="{{ route('vendas.valores.update', $venda->id) }}"
                                method="POST" class="mb-5"
                            >
                                @csrf
                                @method('PATCH')

                                <input type="hidden" name="aba_ativa" value="pagamento">

                                {{-- RESUMO --}}
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 rounded-lg border border-gray-300 p-3">
                                    <div class="min-w-0">
                                        <span class="text-xs text-text-light">
                                            Produtos
                                        </span>

                                        <p id="resumo-produtos" class="font-medium text-text">
                                            R$ {{ number_format($venda->valor_produtos, 2, ',', '.') }}
                                        </p>
                                    </div>

                                    <div class="min-w-0">
                                        <span class="text-xs text-text-light">
                                            Desconto
                                        </span>

                                        <p id="resumo-desconto" class="font-medium text-danger-500">
                                            - R$ {{ number_format($venda->descontos, 2, ',', '.') }}
                                        </p>
                                    </div>

                                    <div class="min-w-0">
                                        <span class="text-xs text-text-light">
                                            Acréscimo
                                        </span>

                                        <p id="resumo-acrescimo" class="font-medium text-text">
                                            R$ {{ number_format($venda->outros_custos, 2, ',', '.') }}
                                        </p>
                                    </div>

                                    <div class="min-w-0">
                                        <span class="text-xs text-text-light">
                                            Total
                                        </span>

                                        <p id="resumo-total" class="font-bold text-primary">
                                            R$ {{ number_format($venda->valor_total, 2, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- DESCONTO E ACRÉSCIMO --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">

                                    {{-- TIPO DE DESCONTO --}}
                                    <div>
                                        <x-input-label for="tipo_desconto" value="Tipo" />

                                        <select name="tipo_desconto" id="tipo_desconto"
                                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                        >
                                            <option value="valor" {{ old('tipo_desconto', 'valor') === 'valor' ? 'selected' : '' }}>
                                                R$
                                            </option>

                                            <option value="percentual" {{ old('tipo_desconto') === 'percentual' ? 'selected' : '' }}>
                                                %
                                            </option>
                                        </select>

                                        <div id="erro-tipo-desconto" class="mt-1 text-sm text-danger-500"></div>
                                    </div>

                                    {{-- DESCONTO --}}
                                    <div>
                                        <x-input-label for="desconto" id="label-desconto" value="Desconto (R$)"/>

                                        <x-input id="desconto" name="desconto" type="text" inputmode="decimal" class="w-full mask-valor"
                                             data-mask-prefix="{{ old('tipo_desconto') === 'percentual' ? '' : 'R$ ' }}" placeholder="R$ 0,00"
                                            :value="old('desconto', number_format($venda->descontos, 2, ',', '.'))"
                                        />

                                        <div id="erro-desconto" class="mt-1 text-sm text-danger-500"></div>
                                    </div>

                                    {{-- ACRÉSCIMO --}}
                                    <div>
                                        <x-input-label for="outros_custos" value="Acréscimo (R$)" />

                                        <x-input id="outros_custos" name="outros_custos" type="text" inputmode="decimal"
                                            class="w-full mask-valor" data-mask-prefix="R$ " placeholder="R$ 0,00"
                                            :value="old('outros_custos', number_format($venda->outros_custos, 2, ',', '.'))"
                                        />

                                        <div id="erro-outros-custos" class="mt-1 text-sm text-danger-500"></div>
                                    </div>

                                </div>

                                <p id="status-salvamento" class="mt-2 text-xs text-text-light"></p>

                            </form>

                            {{-- ADICIONAR RECEBIMENTO --}}
                            <div class="border-t border-gray-300 pt-5">
                                <h3 class="mb-4 font-semibold text-text">
                                    Adicionar recebimento
                                </h3>

                                <form action="{{ route('vendas.recebimentos.store', $venda->id) }}"
                                    method="POST" id="form-recebimento"
                                >
                                    @csrf

                                    <input type="hidden" name="aba_ativa" value="pagamento">

                                    {{-- FORMA DE PAGAMENTO --}}
                                    <div class="mb-4">
                                        <x-input-label for="forma_pagamento_id" value="Forma de pagamento" />

                                        <select name="forma_pagamento_id" id="forma_pagamento_id"
                                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                        >
                                            <option value="">Selecione uma forma de pagamento</option>

                                            @foreach ($formasPagamento as $forma)
                                                <option
                                                    value="{{ $forma->id }}"
                                                    {{ old('forma_pagamento_id') == $forma->id ? 'selected' : '' }}
                                                >
                                                    {{ $forma->nome }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('forma_pagamento_id')
                                            <p class="mt-1 text-sm text-danger-500">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    {{-- VALOR/SITUAÇÃO/DATA DE VENCIMENTO --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <x-input-label for="valor" value="Valor" />

                                            <x-input id="valor" name="valor" type="text" inputmode="decimal"
                                                class="w-full mask-valor" data-mask-prefix="R$ " placeholder="R$ 0,00"
                                                :value="old('valor')"
                                            />
                                        </div>

                                        <div id="campo-data-vencimento" class="mb-4">
                                            <x-input-label for="data_vencimento" value="Data de vencimento"/>

                                            <x-input id="data_vencimento" name="data_vencimento" type="date"
                                                     class="w-full" :value="old('data_vencimento')"
                                            />
                                        </div>

                                        <div>
                                            <x-input-label for="status_recebimento" value="Situação"/>

                                            <select name="status" id="status_recebimento"
                                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                            >
                                                <option value="pago" {{ old('status', 'pago') === 'pago' ? 'selected' : '' }}>
                                                    Pago
                                                </option>

                                                <option value="pendente" {{ old('status') === 'pendente' ? 'selected' : '' }}>
                                                    Pendente
                                                </option>
                                            </select>

                                            @error('status')
                                                <p class="mt-1 text-sm text-danger-500">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- BOTÃO --}}
                                    <div>
                                        <button type="submit" class="w-full block text-center btn bg-primary hover:bg-primary-600 active:bg-primary-700">
                                            Adicionar recebimento
                                        </button>
                                    </div>

                                </form>

                            </div>

                            {{-- RECEBIMENTOS REGISTRADOS --}}
                            <div class="mt-6 border-t border-gray-300 pt-5">
                                <div class="mb-3 flex items-center justify-between">
                                    <h3 class="font-semibold text-text">
                                        Recebimentos
                                    </h3>

                                    <span class="text-xs text-text-light">
                                        {{ $venda->recebimentos->count() }}
                                        {{ $venda->recebimentos->count() === 1 ? 'registro' : 'registros' }}
                                    </span>
                                </div>

                                @if ($venda->recebimentos->isNotEmpty())

                                    <div class="divide-y divide-gray-200">

                                        @foreach ($venda->recebimentos as $recebimento)

                                            <div class="flex items-center justify-between gap-3 py-3">
                                                <div class="min-w-0">
                                                    <p class="break-words font-medium text-text">
                                                        {{ $recebimento->formaPagamento->nome }}
                                                    </p>

                                                    <p class="mt-1 text-xs text-text-light">
                                                        @if ($recebimento->status === 'pago')
                                                            Pago em
                                                            {{ \Carbon\Carbon::parse($recebimento->data_pagamento)->format('d/m/Y') }}
                                                        @elseif ($recebimento->status === 'pendente')
                                                            Vencimento:
                                                            {{ \Carbon\Carbon::parse($recebimento->data_vencimento)->format('d/m/Y') }}
                                                        @else
                                                            Cancelado
                                                        @endif
                                                    </p>
                                                </div>

                                                <div class="shrink-0 text-right">
                                                    <p class="font-semibold text-text">
                                                        R$ {{ number_format($recebimento->valor, 2, ',', '.') }}
                                                    </p>

                                                    @if ($recebimento->status === 'pago')
                                                        <span class="text-xs font-semibold text-primary">
                                                            Pago
                                                        </span>
                                                    @elseif ($recebimento->status === 'pendente')
                                                        <span class="text-xs font-semibold text-beige-700">
                                                            Pendente
                                                        </span>
                                                    @else
                                                        <span class="text-xs font-semibold text-danger-500">
                                                            Cancelado
                                                        </span>
                                                    @endif
                                                </div>
                                                <form action="{{ route('vendas.recebimentos.destroy', ['venda' => $venda, 'recebimento' => $recebimento]) }}" \
                                                      method="POST"
                                                      onsubmit="return confirm('Deseja realmente remover esta parcela?')">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"  class="text-danger-500 hover:text-danger-600 transition"
                                                            title="Remover parcela">

                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-text-light">
                                        Nenhum recebimento registrado nesta venda.
                                    </p>
                                @endif

                            </div>

                        @else

                            <p class="text-sm text-text-light">
                                Adicione um produto para iniciar uma venda e registrar recebimentos.
                            </p>

                        @endif

                    </div>
                </section>

                {{-- PAINEL DIREITO: ITENS DA VENDA --}}
                <section class="flex flex-col w-1/2 min-h-0 overflow-y-auto p-6">
                    <div class="flex min-h-0 flex-1 flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-text">
                                Produtos da venda
                            </h2>

                            <span class="text-sm text-text-light">
                                {{ isset($venda) ? $venda->itens->count() : 0 }}
                                {{ isset($venda) && $venda->itens->count() === 1 ? 'item' : 'itens' }}
                            </span>
                        </div>

                        @if(isset($venda) && $venda->itens->isNotEmpty())
                            <div class="min-h-0 flex-1 space-y-3 overflow-y-auto">
                                @foreach($venda->itens as $item)
                                    <div class="border border-gray-300 rounded-lg p-3">
                                        <div class="flex justify-between gap-3">
                                            <div class="min-w-0">
                                                <h3 class="font-medium text-text">
                                                    {{ $item->produto->nome }}
                                                </h3>

                                                <form action="{{ route('vendas.itens.update', ['venda' => $venda->id, 'item' => $item->id]) }}"
                                                    method="POST" class="flex items-center gap-2 mt-2"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <x-input type="number" name="quantidade" min="0.01" step="0.01" value="{{ $item->quantidade }}" class="w-20 text-sm"
                                                        data-valor-original="{{ $item->quantidade }}" onblur="atualizarQuantidade(this)"/>
                                                    @error('quantidade_item_' . $item->id)
                                                        <p class="mt-1 text-sm text-danger-500">
                                                            {{ $message }}
                                                        </p>
                                                    @enderror

                                                    <span class="text-sm text-text-light">
                                                        x R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}
                                                    </span>
                                                </form>
                                            </div>

                                            <div class="text-right shrink-0">
                                                <p class="font-semibold text-text">
                                                    R$
                                                    {{ number_format($item->quantidade * $item->preco_unitario, 2, ',', '.') }}
                                                </p>
                                                <form action="{{ route('vendas.itens.destroy', ['venda' => $venda->id, 'item' => $item->id]) }}"
                                                    method="POST"
                                                    class="mt-2"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="text-sm text-danger-500 hover:text-danger-600">
                                                        Remover
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex-1 flex flex-col items-center justify-center text-center text-text-light">
                                <p class="text-sm">Nenhum produto adicionado</p>
                                <p class="text-xs mt-1">Os produtos adicionados aparecerão aqui.</p>
                            </div>
                        @endif
                    </div>
                </section>

            </main>

            {{-- BARRA INFERIOR --}}
            <footer class="flex min-h-[68px] shrink-0 items-center justify-between border-t border-gray-300 px-6">
                <div class="flex gap-3">
                    <a href="{{ route('vendas.index') }}"
                       class="rounded-lg border border-primary px-6 py-2 font-semibold text-primary hover:bg-primary hover:text-white">
                        Sair do PDV
                    </a>

                    @isset($venda)
                        @if($venda->itens->isNotEmpty())

                            <form action="{{ route('vendas.finalizar', $venda) }}" method="POST"
                                  onsubmit="return confirm('Deseja realmente finalizar esta venda?');">

                                @csrf

                                <button type="submit"
                                        class="rounded-lg border border-primary px-6 py-2 font-semibold text-white bg-primary hover:bg-primary-700">
                                    Finalizar venda
                                </button>
                            </form>
                        @endif
                    @endisset
                </div>

                <div class="text-right">
                    <span class="mr-3 font-semibold">Total</span>

                    <span id="total-venda" class="text-2xl font-bold">
                        R$ {{ number_format($venda->valor_total ?? 0, 2, ',', '.') }}
                    </span>
                </div>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const produtoSelect = $('#produto_id');
            const precoInput = document.getElementById('preco_produto');

            produtoSelect.on('change', function () {
                const opcaoSelecionada = this.options[this.selectedIndex];

                if (!opcaoSelecionada.value) {
                    precoInput.value = '';
                    return;
                }

                const preco = Number(opcaoSelecionada.dataset.preco);

                precoInput.value = preco.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                precoInput.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            });

            const campoCliente = document.getElementById('cliente_id');

            if (campoCliente && window.jQuery) {
                window.jQuery(document).on('change', '#cliente_id', function () {
                    const formulario = document.getElementById('form-cliente');

                    if (formulario) {
                        formulario.requestSubmit();
                    }
                });
            }
        });

        function atualizarQuantidade(input) {
            const valorOriginal = input.dataset.valorOriginal;
            const valorAtual = input.value;

            // Não envia se o valor não foi alterado.
            if (valorAtual === valorOriginal) {
                return;
            }

            // Não envia campos vazios ou inválidos.
            if (!input.checkValidity()) {
                input.reportValidity();
                return;
            }

            // Envia o formulário.
            input.form.requestSubmit();
        }

        function alternarAba(abaSelecionada) {
            const abas = ['produto', 'cliente', 'pagamento'];

            abas.forEach(function (aba) {
                const botao = document.querySelector(`[data-aba="${aba}"]`);
                const conteudo = document.getElementById(`aba-${aba}`);

                const selecionada = aba === abaSelecionada;

                // Atualiza o conteúdo visível.
                conteudo.classList.toggle('hidden', !selecionada);

                // Atualiza a aparência do botão.
                botao.classList.toggle('bg-primary', selecionada);
                botao.classList.toggle('text-white', selecionada);
                botao.classList.toggle('rounded-full', selecionada);

                botao.setAttribute('aria-selected', selecionada);
            });
        }

        document.querySelectorAll('[data-aba]').forEach(function (botao) {
            botao.addEventListener('click', function () {
                alternarAba(this.dataset.aba);
            });
        });

        const abaInicial = @json(session('aba_ativa', old('aba_ativa', 'produto')));

        alternarAba(abaInicial);

        const formValores = document.getElementById('form-valores');

        if (formValores) {
            const tipoDesconto = document.getElementById('tipo_desconto');
            const campoDesconto = document.getElementById('desconto');
            const campoAcrescimo = document.getElementById('outros_custos');

            let temporizador = null;
            let salvando = false;
            let alterado = false;
            let salvamentoPendente = false;

            // Alterna o prefixo da máscara conforme o tipo de desconto.
            function atualizarTipoDesconto() {
                const percentual = tipoDesconto.value === 'percentual';

                document.getElementById('label-desconto').textContent =
                    percentual ? 'Desconto (%)' : 'Desconto (R$)';

                campoDesconto.dataset.maskPrefix = percentual ? '' : 'R$ ';
                campoDesconto.placeholder = percentual ? '0,00' : 'R$ 0,00';

                if (window.aplicarMascaraValor) {
                    window.aplicarMascaraValor(campoDesconto);
                }
            }

            tipoDesconto.addEventListener('change', function () {
                atualizarTipoDesconto();
                alterado = true;
                agendarSalvamento();
            });

            // Limpa mensagens de erro ao editar o campo.
            function limparErro(campo) {
                const erro = document.getElementById(campo);

                if (erro) {
                    erro.textContent = '';
                }
            }

            campoDesconto.addEventListener('input', function () {
                limparErro('erro-desconto');
                alterado = true;
            });

            campoAcrescimo.addEventListener('input', function () {
                limparErro('erro-outros-custos');
                alterado = true;
            });

            tipoDesconto.addEventListener('change', function () {
                limparErro('erro-tipo-desconto');
            });

            // Formata os valores retornados pelo backend.
            function atualizarResumo(dados) {
                document.getElementById('resumo-produtos').textContent =
                    'R$ ' + dados.valor_produtos;

                document.getElementById('resumo-desconto').textContent =
                    '- R$ ' + dados.descontos;

                document.getElementById('resumo-acrescimo').textContent =
                    'R$ ' + dados.outros_custos;

                document.getElementById('resumo-total').textContent =
                    'R$ ' + dados.valor_total;

                // Atualiza também o total do rodapé do PDV.
                const totalRodape = document.getElementById('total-venda');

                if (totalRodape) {
                    totalRodape.textContent = 'R$ ' + dados.valor_total;
                }
            }

            function exibirErros(erros) {
                const campos = {
                    tipo_desconto: 'erro-tipo-desconto',
                    desconto: 'erro-desconto',
                    outros_custos: 'erro-outros-custos'
                };

                Object.entries(campos).forEach(([campo, elemento]) => {
                    const mensagem = erros[campo]?.[0] ?? '';

                    document.getElementById(elemento).textContent = mensagem;
                });
            }

            async function salvarValores() {
                if (!alterado) {
                    return;
                }

                if (salvando) {
                    salvamentoPendente = true;
                    return;
                }

                salvando = true;
                alterado = false;

                const status = document.getElementById('status-salvamento');
                status.textContent = 'Salvando...';

                try {
                    const resposta = await fetch(formValores.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new FormData(formValores)
                    });

                    const dados = await resposta.json();

                    if (resposta.status === 422) {
                        exibirErros(dados.errors ?? {});
                        status.textContent = 'Não foi possível salvar os valores.';
                        return;
                    }

                    if (!resposta.ok) {
                        throw new Error('Erro ao salvar os valores.');
                    }

                    exibirErros({});
                    atualizarResumo(dados);

                    status.textContent = 'Valores salvos com sucesso.';
                } catch (erro) {
                    status.textContent = 'Erro ao salvar. Tente novamente.';
                    alterado = true;
                } finally {
                    salvando = false;

                    if (salvamentoPendente) {
                        salvamentoPendente = false;
                        agendarSalvamento();
                    }
                }
            }

            function agendarSalvamento() {
                clearTimeout(temporizador);

                temporizador = setTimeout(salvarValores, 400);
            }

            // Salva ao perder o foco dos campos.
            [campoDesconto, campoAcrescimo].forEach(campo => {
                campo.addEventListener('blur', agendarSalvamento);
            });

            // Se o usuário alterar outro campo enquanto salva,
            // agenda uma nova atualização ao terminar a requisição atual.
            formValores.addEventListener('input', function () {
                clearTimeout(temporizador);
            });

            atualizarTipoDesconto();
        }
    </script>
</body>
</html>
