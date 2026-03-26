<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1 class="inline text-primary font-semibold text-2xl leading-tight">
                {{ __('Alterar Cliente') }}
            </h1>
        </div>
    </x-slot>
    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div class="md:col-span-1">
                                <x-input-label for="status" :value="__('Status')" />
                                <div class="mt-1 flex items-center gap-3 p-[6px] border border-gray-300 rounded-md shadow-sm">
                                    <input type="hidden" name="status" value="0">
                                    <x-text-input id="status" name="status" type="checkbox" value="1"
                                        class="rounded text-primary shadow-sm focus:ring-primary"
                                        :checked="old('status', $cliente->status) == 1" 
                                    />
                                    <span>Ativo</span>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="nome" :value="__('Nome')" class="required" />
                                <x-text-input id="nome" name="nome" type="text" class="block mt-1 w-full"
                                    :value="old('nome', $cliente->nome)" />
                            </div>
                            <div class="md:col-span-1">
                                <x-input-label for="tipo" :value="__('tipo')" />

                                <div class="p-[6px] border border-gray-300 rounded-md mt-1 p-2 flex flex-wrap gap-4">
                                    <label for="fisico" class="flex items-center gap-2 cursor-pointer">
                                        <x-text-input id="fisico" name="tipo" type="radio" value="f"
                                            :checked="old('tipo', 'f') === 'f'" />
                                        <span>Física</span>
                                    </label>

                                    <label for="juridica" class="flex items-center gap-2 cursor-pointer">
                                        <x-text-input id="juridica" name="tipo" type="radio" value="j"
                                            :checked="old('tipo', 'f') === 'j'" />
                                        <span>Jurídica</span>
                                    </label>
                                </div>
                            </div>
                            <div id="cpf-field">
                                <x-input-label for="cpf" :value="__('CPF')" />
                                <x-text-input id="cpf" name="cpf" type="text"
                                    class="block mt-1 w-full mask-cpf" :value="old('cpf', $cliente->cpf)" />
                            </div>
                            <div id="cnpj-field" class="hidden">
                                <x-input-label for="cnpj" :value="__('CNPJ')" />
                                <x-text-input id="cnpj" name="cnpj" type="text"
                                    class="block mt-1 w-full mask-cnpj" :value="old('cnpj', $cliente->cnpj)" />
                            </div>
                            <div id="rg-field">
                                <x-input-label for="rg" :value="__('RG')" />
                                <x-text-input id="rg" name="rg" type="text" class="block mt-1 w-full"
                                    :value="old('rg', $cliente->rg)" />
                            </div>
                            <div id="ie-field" class="hidden">
                                <x-input-label for="ie" :value="__('Inscrição Estadual')" />
                                <x-text-input id="ie" name="ie" type="text" class="block mt-1 w-full"
                                    :value="old('ie', $cliente->ie)" />
                            </div>
                            <div id="data-nascimento">
                                <x-input-label for="data_nascimento" :value="__('Data nascimento')" />
                                <x-text-input id="data_nascimento" name="data_nascimento" type="date"
                                    class="block mt-1 w-full" :value="old('data_nascimento', optional($cliente->data_nascimento)->format('Y-m-d'))" />
                            </div>

                            <h2 class="md:col-span-5 block text-primary font-semibold text-xl leading-tight mt-8">
                                {{ __('Contato') }}
                            </h2>
                            <div>
                                <x-input-label for="celular" :value="__('Celular')" />
                                <x-text-input id="celular" name="celular" type="text"
                                    class="block mt-1 w-full mask-celular" :value="old('celular', $cliente->celular)" />
                            </div>
                            <div>
                                <x-input-label for="telefone" :value="__('Telefone')" />
                                <x-text-input id="telefone" name="telefone" type="text"
                                    class="block mt-1 w-full mask-telefone" :value="old('telefone', $cliente->telefone)" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="block mt-1 w-full"
                                    :value="old('email', $cliente->email)" />
                            </div>

                            <h2 class="md:col-span-5 block text-primary font-semibold text-xl leading-tight mt-8">
                                {{ __('Endereço') }}
                            </h2>

                            <div>
                                <x-input-label for="cep" :value="__('CEP')" />
                                <x-text-input id="cep" name="cep" type="text"
                                    class="block mt-1 w-full mask-cep" :value="old('cep', $cliente->cep)" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="logradouro" :value="__('Logradouro')" />
                                <x-text-input id="logradouro" name="logradouro" type="text" class="block mt-1 w-full"
                                    :value="old('logradouro', $cliente->logradouro)" />
                            </div>
                            <div>
                                <x-input-label for="numero" :value="__('Número')" />
                                <x-text-input id="numero" name="numero" type="text" class="block mt-1 w-full"
                                    :value="old('numero', $cliente->numero)" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="complemento" :value="__('Complemento')" />
                                <x-text-input id="complemento" name="complemento" type="text"
                                    class="block mt-1 w-full" :value="old('complemento', $cliente->complemento)" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="bairro" :value="__('Bairro')" />
                                <x-text-input id="bairro" name="bairro" type="text" class="block mt-1 w-full"
                                    :value="old('bairro', $cliente->bairro)" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="cidade" :value="__('Cidade')" />
                                <x-text-input id="cidade" name="cidade" type="text" class="block mt-1 w-full"
                                    :value="old('cidade', $cliente->cidade)" />
                            </div>
                            <div class="md:col-span-1">
                                <x-input-label for="uf" value="UF" />

                                <select name="uf" id="uf"
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary">
                                    <option value="">Selecione</option>

                                    @foreach (['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                        <option value="{{ $uf }}" @selected(old('uf', $cliente->uf) === $uf)>
                                            {{ $uf }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <h2 class="md:col-span-5 block text-primary font-semibold text-xl leading-tight mt-8">
                                {{ __('Dados adicionais') }}
                            </h2>
                            <div class="md:col-span-5">
                                <x-input-label for="observacoes" :value="__('Observações')" />
                                <textarea name="observacoes" id="observacoes">{{ old('observacoes', $cliente->cliente->observacoes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <x-form-button :cancelUrl="route('clientes.index')" submitText="Salvar"/>

            </form>
        </div>
    </div>
</x-app-layout>