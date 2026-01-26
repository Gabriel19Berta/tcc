<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1 class="inline text-primary font-semibold text-2xl leading-tight">
                {{ __('Cadastrar Cliente') }}
            </h1>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="#" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div class="md:col-span-2">
                                <x-input-label for="nome" :value="__('Nome')" class="required" />
                                <x-text-input id="nome" name="nome" type="text" class="block mt-1 w-full"
                                    :value="old('nome')" required />
                            </div>
                            <div class="md:col-span-1">
                                <x-input-label for="tipo" :value="__('Tipo')" />

                                <div class="border border-gray-300 rounded-md mt-1 p-[6px] flex flex-wrap gap-4">
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
                                    class="block mt-1 w-full mask-cpf" :value="old('cpf')" />
                            </div>
                            <div id="cnpj-field" class="hidden">
                                <x-input-label for="cnpj" :value="__('CNPJ')" />
                                <x-text-input id="cnpj" name="cnpj" type="text"
                                    class="block mt-1 w-full mask-cnpj" :value="old('cnpj')" />
                            </div>
                            <div>
                                <x-input-label for="rg" :value="__('RG')" />
                                <x-text-input id="rg" name="rg" type="text" class="block mt-1 w-full"
                                    :value="old('rg')" />
                            </div>
                            <div>
                                <x-input-label for="dt_nascimento" :value="__('Data nascimento')" />
                                <x-text-input id="dt_nascimento" name="dt_nascimento" type="date" class="block mt-1 w-full"
                                    :value="old('dt_nascimento')" />
                            </div>

                            <h2 class="md:col-span-5 block text-primary font-semibold text-xl leading-tight mt-8">
                                {{ __('Contato') }}
                            </h2>
                            <div>
                                <x-input-label for="celular" :value="__('Celular')" />
                                <x-text-input id="celular" name="celular" type="text" class="block mt-1 w-full mask-celular"
                                    :value="old('celular')" />
                            </div>
                            <div>
                                <x-input-label for="telefone" :value="__('Telefone')" />
                                <x-text-input id="telefone" name="telefone" type="text" class="block mt-1 w-full mask-telefone"
                                    :value="old('telefone')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="block mt-1 w-full"
                                    :value="old('email')" />
                            </div>
                            
                            <h2 class="md:col-span-5 block text-primary font-semibold text-xl leading-tight mt-8">
                                {{ __('Endereço') }}
                            </h2>
                            
                            <div>
                                <x-input-label for="cep" :value="__('CEP')" />
                                <x-text-input id="cep" name="cep" type="text" class="block mt-1 w-full mask-cep"
                                    :value="old('cep')" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="logradouro" :value="__('Logradouro')" />
                                <x-text-input id="logradouro" name="logradouro" type="text" class="block mt-1 w-full"
                                    :value="old('logradouro')" />
                            </div>
                            <div>
                                <x-input-label for="numero" :value="__('Número')" />
                                <x-text-input id="numero" name="numero" type="text" class="block mt-1 w-full"
                                    :value="old('numero')" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="complemento" :value="__('Complemento')" />
                                <x-text-input id="complemento" name="complemento" type="text" class="block mt-1 w-full"
                                    :value="old('complemento')" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="bairro" :value="__('Bairro')" />
                                <x-text-input id="bairro" name="bairro" type="text" class="block mt-1 w-full"
                                    :value="old('bairro')" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="cidade" :value="__('Cidade')" />
                                <x-text-input id="cidade" name="cidade" type="text" class="block mt-1 w-full"
                                    :value="old('cidade')" />
                            </div>
                            {{-- Adicionar UF --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>