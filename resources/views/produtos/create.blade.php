<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Cadastrar Produto') }}
            </h1>
        </div>
    </x-slot>

    <div class="py-4">
        <form action="{{ route('produtos.store') }}" method="POST">
            @csrf
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="nome" :value="__('Nome')" class="required" />
                            <x-text-input id="nome" name="nome" type="text" class="w-full"
                                :value="old('nome')" />
                        </div>
                        {{-- MARCA --}}
                        <div class="md:col-span-1">
                            <x-input-label for="nome" :value="__('Marca')" />
                            <select name="marca_id" id="marca_id" class="select2">
                                <option value=""></option>
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca['id'] }}"
                                        {{ old('marca_id') == $marca['id'] ? 'selected' : '' }}>
                                        {{ $marca['nome'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- TIPO PRODUTO --}}
                        <div class="md:col-span-1">
                            <x-input-label for="nome" :value="__('Tipo de produto')" />
                            <select name="tipo_produto_id" id="tipo_produto_id" class="select2">
                                <option value=""></option>
                                @foreach ($tipo_produtos as $tipo_produto)
                                    <option value="{{ $tipo_produto['id'] }}"
                                    {{ old('tipo_produto_id') == $tipo_produto['id'] ? 'selected' : '' }}>
                                    {{ $tipo_produto['nome'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <x-input-label for="peso" :value="__('Peso')" />
                            <x-text-input id="peso" name="peso" type="number" class="w-full"
                                min="0" step="any" :value="old('peso')" />
                        </div>
                        <h2 class="md:col-span-5">
                            {{ __('Valores') }}
                        </h2>
                        <div class="md:col-span-1">
                            <x-input-label for="preco_custo" :value="__('Preço de custo')" />
                            <x-text-input id="preco_custo" name="preco_custo" type="text" class="w-full mask-valor" placeholder="R$ 0,00"
                                min="0" :value="old('preco_custo', 0.00)" />
                        </div>
                        <div class="md:col-span-1">
                            <x-input-label for="preco_venda" :value="__('Preço de venda')" />
                            <x-text-input id="preco_venda" name="preco_venda" type="text" class="w-full mask-valor" placeholder="R$ 0,00"
                                min="0" :value="old('preco_venda', 0.00)" />
                        </div>
                        <h2 class="md:col-span-5">
                            {{ __('Estoque') }}
                        </h2>
                        <div class="md:col-span-1">
                            <x-input-label for="controla_estoque" :value="__('Controlar estoque')" />
                            <div class="contain-check">
                                <input type="hidden" name="controla_estoque" value="0">
                                <x-text-input id="controla_estoque" name="controla_estoque" type="checkbox" value="1"
                                    class="rounded text-primary shadow-sm focus:ring-primary"
                                    :checked="old('controla_estoque', true)" 
                                />
                                <span>Ativo</span>
                            </div>
                        </div>
                        <div class="md:col-span-1" id="estoque">
                            <x-input-label for="quantidade" :value="__('Quantidade em estoque')" class="required" />
                            <x-text-input id="quantidade" name="quantidade" type="number" class="w-full"
                                min="0" step="any" :value="old('quantidade')" />
                        </div>
                        <h2 class="md:col-span-5">
                            {{ __('Dados adicionais') }}
                        </h2>
                        <div class="md:col-span-5">
                            <x-input-label for="observacoes" :value="__('Observações')" />
                            <textarea name="observacoes" id="observacoes">{{ old('observacoes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <x-form-button :cancelUrl="route('produtos.index')" />
        </form>
    </div>
</x-app-layout>

<script>
    var check = document.getElementById('controla_estoque');
    var estoque = document.getElementById('estoque');

    check.addEventListener('click', function () {
        if(check.checked) {
            estoque.style.display = "block";
        } else {
            estoque.style.display = "none";
        }
    })
</script>