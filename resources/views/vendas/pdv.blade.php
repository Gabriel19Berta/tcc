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
                        <button type="button"
                                class="flex-1 rounded-full bg-primary px-4 py-3 font-semibold text-white">
                            Produto
                        </button>

                        <button type="button"
                                class="flex-1 px-4 py-3 font-semibold">
                            Cliente
                        </button>

                        <button type="button"
                                class="flex-1 px-4 py-3 font-semibold">
                            Pagamento
                        </button>
                    </nav>

                    {{-- CONTEÚDO DA ABA --}}
                    <div class="flex-1">
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
                                    required
                                >
                                    <option value="">Selecione um produto</option>

                                    @foreach ($produtos as $produto)
                                        <option
                                            value="{{ $produto->id }}"
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
                                         required :value="old('quantidade', '1')" />

                                @error('quantidade')
                                    <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <x-input-label for="preco_produto" :value="__('Preço')" />
                                <x-input id="preco_produto" name="preco_produto" type="text" class="w-full mask-valor" placeholder="R$ 0,00"
                                         min="0" :value="old('preco_produto', 0.00)" />
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
                </section>

                {{-- PAINEL DIREITO: ITENS DA VENDA --}}
                <section class="w-1/2 overflow-auto p-6">
                    <div class="flex h-full flex-col items-center justify-center text-center text-gray-400">
                        <i class="fa-solid fa-seedling mb-4 text-7xl text-beige-600"></i>

                        <p class="text-lg font-medium">
                            Nenhum produto adicionado
                        </p>

                        <p class="mt-2 text-sm">
                            Os produtos da venda aparecerão aqui.
                        </p>
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
                    <span class="text-2xl font-bold">R$ 0,00</span>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
