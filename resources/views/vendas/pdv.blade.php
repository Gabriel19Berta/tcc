@use('App\Support\Formatador')
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
        {{-- Comportamentos da tela: resources/js/pdv.js (aba inicial informada pelo backend). --}}
        <div id="pdv" class="flex min-h-screen flex-col bg-white text-gray-700 lg:h-screen"
            data-aba-inicial="{{ session('aba_ativa', old('aba_ativa', 'produto')) }}">

            {{-- CABEÇALHO --}}
            <header class="flex shrink-0 flex-wrap items-center justify-between gap-x-6 gap-y-3 bg-primary px-4 py-3 text-white sm:px-6 lg:h-[68px] lg:flex-nowrap lg:py-0">
                <div class="flex w-full min-w-0 flex-wrap items-center gap-x-6 gap-y-2 lg:w-auto">
                    <h1 class="whitespace-nowrap text-2xl font-bold text-white">
                        PDV @isset($venda) {{ ' - ' . $venda->id }} @endisset
                    </h1>

                    {{--
                        FUNCIONÁRIO RESPONSÁVEL
                        - Venda existente: formulário próprio, salvo ao alterar a seleção.
                        - Nova venda: o select pertence ao formulário de produto (atributo form)
                          e é enviado junto com o primeiro produto, quando a venda é criada.
                    --}}
                    @isset($venda)
                        <form id="form-funcionario" action="{{ route('vendas.funcionario.update', $venda->id) }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            @method('PATCH')
                    @endisset

                    <div class="flex w-full flex-col gap-1 sm:w-auto sm:flex-row sm:items-center sm:gap-2">
                        <label for="funcionario_id" class="text-sm font-medium text-white whitespace-nowrap">
                            Funcionário responsável:
                        </label>

                        <select id="funcionario_id" name="funcionario_id"
                            class="w-full rounded-lg border-gray-300 text-sm text-gray-700 sm:w-56"
                            @isset($venda)
                                onchange="this.form.requestSubmit()"
                            @else
                                form="form-produto"
                            @endisset
                        >
                            <option value="">Nenhum funcionário</option>

                            @foreach ($funcionarios as $funcionario)
                                <option value="{{ $funcionario->id }}"
                                    @selected((string) $funcionario->id === (string) $funcionarioSelecionadoId)>
                                    {{ $funcionario->pessoa->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @isset($venda)
                        </form>
                    @endisset
                </div>

                <div class="flex w-full items-center justify-between gap-4 sm:w-auto">
                    <form method="GET" action="{{ route('vendas.index') }}" class="shrink-0">
                        <button type="submit" class="whitespace-nowrap py-2 text-sm font-semibold hover:underline">
                            Consultar vendas
                        </button>
                    </form>

                    <div class="min-w-0">
                        <select id="venda_aberta" class="w-full max-w-full rounded-lg border-gray-300 text-sm text-gray-700"
                            onchange="if (this.value) window.location.href = this.value">
                            <option value="">Continuar venda...</option>

                            @foreach ($vendasAbertas as $vendaAberta)
                                <option value="{{ route('vendas.edit', $vendaAberta->id) }}">
                                    Venda #{{ $vendaAberta->id }}
                                    - {{ Formatador::reais($vendaAberta->valor_total) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </header>

            {{-- CONTEÚDO PRINCIPAL --}}
            <main class="flex flex-1 flex-col lg:min-h-0 lg:flex-row lg:overflow-hidden">

                {{-- PAINEL ESQUERDO --}}
                <section class="flex w-full min-w-0 flex-col border-b border-gray-300 p-4 sm:p-6 lg:min-h-0 lg:w-1/2 lg:overflow-y-auto lg:border-b-0 lg:border-r">

                    {{-- MENSAGENS DE ERRO --}}
                    @if ($errors->has('venda'))
                        <div class="mb-4">
                            <x-alert type="error">
                                {{ $errors->first('venda') }}
                            </x-alert>
                        </div>
                    @endif

                    @if ($errors->has('funcionario_id'))
                        <div class="mb-4">
                            <x-alert type="error">
                                {{ $errors->first('funcionario_id') }}
                            </x-alert>
                        </div>
                    @endif

                    {{-- NAVEGAÇÃO --}}
                    <nav class="mb-2 flex shrink-0 overflow-hidden rounded-full border border-primary">
                        <button type="button" data-aba="produto" class="flex-1 rounded-full bg-primary px-2 py-3 text-sm font-semibold text-white sm:px-4 sm:text-base"
                            aria-selected="true">
                            Produto
                        </button>

                        <button type="button" data-aba="cliente" class="flex-1 px-2 py-3 text-sm font-semibold sm:px-4 sm:text-base" aria-selected="false">
                            Cliente
                        </button>

                        <button type="button" data-aba="pagamento" class="flex-1 px-2 py-3 text-sm font-semibold sm:px-4 sm:text-base" aria-selected="false">
                            Pagamento
                        </button>
                    </nav>

                    {{-- CONTEÚDO DA ABA --}}
                    {{-- ABA PRODUTO --}}
                    <div id="aba-produto" class="flex-1">
                        <h2 class="mb-8 text-lg font-semibold text-primary">
                            Adicionar produto
                        </h2>

                        <form id="form-produto" action="{{ route('vendas.itens.store') }}" method="POST">
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
                                            {{ $produto->nome }} - {{ Formatador::reais($produto->preco_venda) }}
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
                                                    {{ Formatador::cpf($pessoa->cpf) ?: 'Não informado' }}
                                                @elseif($pessoa->tipo == 'j')
                                                    {{ Formatador::cnpj($pessoa->cnpj) ?: 'Não informado' }}
                                                @else
                                                    Não informado
                                                @endif
                                            </p>
                                        </div>

                                        {{-- TELEFONE --}}
                                        <div class="min-w-0">
                                            <span class="text-xs text-text-light">Telefone</span>
                                            <p class="text-sm text-text break-words">
                                                {{ Formatador::telefone($pessoa->telefone) ?: 'Não informado' }}
                                            </p>
                                        </div>

                                        {{-- CELULAR --}}
                                        <div class="min-w-0">
                                            <span class="text-xs text-text-light">Celular</span>
                                            <p class="text-sm text-text break-words">
                                                {{ Formatador::celular($pessoa->celular) ?: 'Não informado' }}
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

                                {{-- TIPO, DESCONTO, ACRÉSCIMO E TOTAL --}}
                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-[6rem_repeat(3,minmax(0,1fr))]">

                                    {{-- TIPO DE DESCONTO --}}
                                    <div>
                                        <x-input-label for="tipo_desconto" value="Tipo" />

                                        <select name="tipo_desconto" id="tipo_desconto"
                                            class="block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
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
                                            :value="old('desconto', Formatador::moeda($venda->descontos))"
                                        />

                                        <div id="erro-desconto" class="mt-1 text-sm text-danger-500"></div>
                                    </div>

                                    {{-- ACRÉSCIMO --}}
                                    <div>
                                        <x-input-label for="outros_custos" value="Acréscimo (R$)" />

                                        <x-input id="outros_custos" name="outros_custos" type="text" inputmode="decimal"
                                            class="w-full mask-valor" data-mask-prefix="R$ " placeholder="R$ 0,00"
                                            :value="old('outros_custos', Formatador::moeda($venda->outros_custos))"
                                        />

                                        <div id="erro-outros-custos" class="mt-1 text-sm text-danger-500"></div>
                                    </div>

                                    <div>
                                        <x-input-label for="resumo-total" value="Total" />

                                        <x-input id="resumo-total" type="text" readonly tabindex="-1"
                                            class="w-full cursor-default bg-gray-100 font-semibold text-primary"
                                            :value="Formatador::reais($venda->valor_total)"
                                        />
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

                                    {{-- FORMA DE PAGAMENTO / VALOR RESTANTE --}}
                                    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div class="sm:col-span-2">
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

                                        {{-- Total da venda menos os recebimentos não cancelados --}}
                                        <div class="flex items-center justify-between gap-2 rounded-lg bg-gray-100 px-3 py-2 text-sm sm:mt-6 sm:self-start border border-gray-300">
                                            <span class="text-text-light">Valor restante:</span>
                                            <span id="valor-restante" class="whitespace-nowrap font-semibold text-primary">
                                                {{ Formatador::reais($venda->valorRestante()) }}
                                            </span>
                                        </div>
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
                                                            {{ Formatador::data($recebimento->data_pagamento, 'd/m/Y') }}
                                                        @elseif ($recebimento->status === 'pendente')
                                                            Vencimento:
                                                            {{ Formatador::data($recebimento->data_vencimento, 'd/m/Y') }}
                                                        @else
                                                            {{ ucfirst($recebimento->status) }}
                                                        @endif
                                                    </p>
                                                </div>

                                                <div class="shrink-0 text-right">
                                                    <p class="font-semibold text-text">
                                                        {{ Formatador::reais($recebimento->valor) }}
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
                                                            {{ ucfirst($recebimento->status) }}
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
                <section class="flex w-full min-w-0 flex-col p-4 sm:p-6 lg:min-h-0 lg:w-1/2 lg:overflow-y-auto">
                    <div class="flex flex-1 flex-col lg:min-h-0">
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
                            <div class="space-y-3 lg:min-h-0 lg:flex-1 lg:overflow-y-auto">
                                @foreach($venda->itens as $item)
                                    <div class="border border-gray-300 rounded-lg p-3">
                                        {{-- Nome e subtotal --}}
                                        <div class="flex items-start justify-between gap-3">
                                            <h3 class="min-w-0 break-words font-medium text-text">
                                                {{ $item->produto->nome }}
                                            </h3>

                                            <p class="shrink-0 font-semibold text-text">
                                                {{ Formatador::reais($item->quantidade * $item->preco_unitario) }}
                                            </p>
                                        </div>

                                        {{-- Quantidade × preço unitário e remoção --}}
                                        <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                                            <form action="{{ route('vendas.itens.update', ['venda' => $venda->id, 'item' => $item->id]) }}"
                                                method="POST" class="flex min-w-0 items-center gap-2"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <x-input type="number" name="quantidade" min="0.01" step="0.01" value="{{ $item->quantidade }}" class="h-10 w-24 text-sm"
                                                    data-quantidade-item data-valor-original="{{ $item->quantidade }}"/>

                                                <span class="whitespace-nowrap text-sm text-text-light">
                                                    x {{ Formatador::reais($item->preco_unitario) }}
                                                </span>
                                            </form>

                                            <form action="{{ route('vendas.itens.destroy', ['venda' => $venda->id, 'item' => $item->id]) }}"
                                                method="POST" class="ml-auto"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="inline-flex h-10 items-center rounded-lg px-3 text-sm text-danger-500 hover:bg-gray-100 hover:text-danger-600">
                                                    Remover
                                                </button>
                                            </form>
                                        </div>

                                        @error('quantidade_item_' . $item->id)
                                            <p class="mt-1 text-sm text-danger-500">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-1 flex-col items-center justify-center py-8 text-center text-text-light">
                                <p class="text-sm">Nenhum produto adicionado</p>
                                <p class="text-xs mt-1">Os produtos adicionados aparecerão aqui.</p>
                            </div>
                        @endif
                    </div>
                </section>

            </main>

            {{-- BARRA INFERIOR --}}
            <footer class="sticky bottom-0 z-10 flex min-h-[68px] shrink-0 flex-col-reverse gap-3 border-t border-gray-300 bg-white px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:py-0">
                <div class="flex gap-3">
                    <a href="{{ route('vendas.index') }}"
                       class="flex-1 whitespace-nowrap rounded-lg border border-danger-500 px-4 py-2 text-center font-semibold text-danger-500 hover:bg-danger-500 hover:text-white sm:flex-none sm:px-6">
                        Sair do PDV
                    </a>

                    @isset($venda)
                        @if($venda->itens->isNotEmpty())
                            <form action="{{ route('vendas.finalizar', $venda) }}" method="POST" class="flex-1 sm:flex-none"
                                  data-confirm="Deseja realmente finalizar esta venda?">

                                @csrf

                                <button type="submit"
                                        class="w-full whitespace-nowrap rounded-lg border border-primary px-4 py-2 font-semibold text-white bg-primary hover:bg-primary-700 sm:px-6">
                                    Finalizar venda
                                </button>
                            </form>
                        @endif
                    @endisset
                </div>

                <div class="flex items-baseline justify-between gap-3 sm:block sm:text-right">
                    <span class="font-semibold sm:mr-3">Total</span>

                    <span id="total-venda" class="whitespace-nowrap text-xl font-bold sm:text-2xl">
                        {{ Formatador::reais($venda->valor_total ?? 0) }}
                    </span>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
