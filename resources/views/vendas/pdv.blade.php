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
                        <select
                            id="venda_aberta"
                            class="rounded-lg border-gray-300 text-sm"
                            onchange="if (this.value) window.location.href = this.value"
                        >
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
            <main class="flex min-h-0 flex-1">

                {{-- PAINEL ESQUERDO --}}
                <section class="flex w-1/2 flex-col border-r border-gray-300 p-6">

                    {{-- NAVEGAÇÃO --}}
                    <nav class="mb-2 flex overflow-hidden rounded-full border border-primary">
                        <button type="button" data-aba="produto" class="flex-1 rounded-full bg-primary px-4 py-3 font-semibold text-white"
                            aria-selected="true"
                        >
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

                                <select
                                    name="produto_id"
                                    id="produto_id"
                                    class="select2"
                                >
                                    <option value="">Selecione um produto</option>

                                    @foreach ($produtos as $produto)
                                        <option
                                            value="{{ $produto->id }}"
                                            data-preco="{{ $produto->preco_venda }}"
                                            {{ old('produto_id') == $produto->id ? 'selected' : '' }}
                                        >
                                            {{ $produto->nome }}
                                            - R$ {{ number_format($produto->preco_venda, 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('produto_id')
                                    <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- QUANTIDADE --}}
                            <div class="mb-6">
                                <x-input-label for="quantidade" :value="__('Quantidade')" />
                                <x-input id="quantidade" name="quantidade" type="number" min="0.01" step="0.01" class="w-full"
                                         :value="old('quantidade', '1')" />
                            </div>

                            <div class="mb-6">
                                <x-input-label for="preco_produto" :value="__('Preço')" />
                                <x-input id="preco_produto" name="preco_produto" type="text" class="w-full mask-valor" placeholder="R$ 0,00"
                                         min="0" :value="old('preco_produto')" />
                            </div>

                            {{-- BOTÃO --}}
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    class="btn bg-primary hover:bg-primary-600 active:bg-primary-700"
                                >
                                    Adicionar produto
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ABA CLIENTE --}}
                    <div id="aba-cliente" class="flex-1 hidden">
                        <h2 class="mb-8 text-lg font-semibold text-primary">
                            Selecionar cliente
                        </h2>

                        @if(isset($venda))
                            <form action="{{ route('vendas.cliente.update', $venda->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-6">
                                    <x-input-label for="cliente_id" :value="__('Cliente')" />

                                    <select name="cliente_id" id="cliente_id" class="select2">
                                        <option value="">Venda sem cliente</option>

                                        @foreach($clientes as $cliente)
                                            <option
                                                value="{{ $cliente->id }}"
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

                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="btn bg-primary hover:bg-primary-600 active:bg-primary-700"
                                    >
                                        Salvar cliente
                                    </button>
                                </div>

                            </form>

                            @if($venda->cliente && $venda->cliente->pessoa)

                                <div class="mt-8 rounded-lg border border-gray-300 p-4">

                                    <h3 class="mb-3 font-semibold text-text">
                                        Cliente selecionado
                                    </h3>

                                    <p class="text-sm text-text">
                                        {{ $venda->cliente->pessoa->nome }}
                                    </p>

                                    @if($venda->cliente->pessoa->cpf)
                                        <p class="mt-1 text-sm text-text-light">
                                            CPF: {{ $venda->cliente->pessoa->cpf }}
                                        </p>
                                    @endif

                                    @if($venda->cliente->pessoa->cnpj)
                                        <p class="mt-1 text-sm text-text-light">
                                            CNPJ: {{ $venda->cliente->pessoa->cnpj }}
                                        </p>
                                    @endif

                                    @if($venda->cliente->pessoa->celular)
                                        <p class="mt-1 text-sm text-text-light">
                                            Celular: {{ $venda->cliente->pessoa->celular }}
                                        </p>
                                    @endif

                                </div>

                            @else

                                <p class="mt-6 text-sm text-text-light">
                                    Nenhum cliente selecionado para esta venda.
                                </p>

                            @endif

                        @else

                            <p class="text-sm text-text-light">
                                Adicione pelo menos um produto para iniciar uma venda e selecionar um cliente.
                            </p>

                        @endif

                    </div>

                    {{-- ABA PAGAMENTO --}}
                    <div id="aba-pagamento" class="flex-1 hidden">
                        <h2 class="mb-8 text-lg font-semibold text-primary">
                            Pagamento
                        </h2>

                        <p class="text-sm text-text-light">
                            As informações de pagamento serão implementadas posteriormente.
                        </p>
                    </div>
                </section>

                {{-- PAINEL DIREITO: ITENS DA VENDA --}}
                <section class="w-1/2 overflow-auto p-6">
                    <div class="flex flex-col h-full">
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
                            <div class="flex-1 overflow-y-auto space-y-3">
                                @foreach($venda->itens as $item)
                                    <div class="border border-gray-300 rounded-lg p-3">
                                        <div class="flex justify-between gap-3">
                                            <div class="min-w-0">
                                                <h3 class="font-medium text-text">
                                                    {{ $item->produto->nome }}
                                                </h3>

                                                <form
                                                    action="{{ route('vendas.itens.update', [
                                                        'venda' => $venda->id,
                                                        'item' => $item->id
                                                    ]) }}"
                                                    method="POST"
                                                    class="flex items-center gap-2 mt-2"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <x-input type="number" name="quantidade" min="0.01" step="0.01" value="{{ $item->quantidade }}" class="w-20 text-sm" 
                                                        data-valor-original="{{ $item->quantidade }}" onblur="atualizarQuantidade(this)"/>

                                                    <span class="text-sm text-text-light">
                                                        x R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}
                                                    </span>
                                                </form>
                                            </div>

                                            <div class="text-right shrink-0">
                                                <p class="font-semibold text-text">
                                                    R$
                                                    {{ number_format(
                                                        $item->quantidade * $item->preco_unitario,
                                                        2,
                                                        ',',
                                                        '.'
                                                    ) }}
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
                </div>

                <div class="text-right">
                    <span class="mr-3 font-semibold">Total</span>
                    <span class="text-2xl font-bold">R$ {{ number_format($venda->valor_total ?? 0, 2, ',', '.') }}</span>
                </div>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const produtoSelect = $('#produto_id');
            const precoInput = document.getElementById('preco_produto');

            console.log('Select de produto:', produtoSelect);

            produtoSelect.on('change', function () {
                console.log('Evento change acionado');

                const opcaoSelecionada = this.options[this.selectedIndex];

                console.log('Opção selecionada:', opcaoSelecionada);

                if (!opcaoSelecionada.value) {
                    precoInput.value = '';
                    return;
                }

                const preco = Number(opcaoSelecionada.dataset.preco);

                console.log('Preço encontrado:', preco);

                precoInput.value = preco.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                precoInput.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            });
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
    </script>
</body>
</html>
