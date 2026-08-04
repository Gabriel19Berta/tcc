<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Alterar Dados do Funcionários') }}
            </h1>
        </div>
    </x-slot>
    <div class="py-4">
        <form action="{{ route('funcionarios.update', $funcionario->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-1">
                            <x-input-label for="status" :value="__('Status')" />
                            <div class="contain-check">
                                <input type="hidden" name="status" value="0">
                                <x-input id="status" name="status" type="checkbox" value="1"
                                    class="rounded text-primary shadow-sm focus:ring-primary"
                                    :checked="old('status', $funcionario->status) == 1" 
                                />
                                <span>Ativo</span>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="nome" :value="__('Nome')" class="required" />
                            <x-input id="nome" name="nome" type="text" class="w-full"
                                :value="old('nome', $funcionario->nome)" />
                        </div>
                        <div class="md:col-span-1">
                            <x-input-label for="tipo" :value="__('Tipo')" />

                            <div class="contain-check">
                                <label for="fisico" class="flex items-center gap-2 cursor-pointer">
                                    <x-input id="fisico" name="tipo" type="radio" value="f"
                                        :checked="old('tipo', $funcionario->tipo) === 'f'" />
                                    <span>Física</span>
                                </label>

                                <label for="juridica" class="flex items-center gap-2 cursor-pointer">
                                    <x-input id="juridica" name="tipo" type="radio" value="j"
                                        :checked="old('tipo', $funcionario->tipo) === 'j'" />
                                    <span>Jurídica</span>
                                </label>
                            </div>
                        </div>
                        <div id="cpf-field">
                            <x-input-label for="cpf" :value="__('CPF')" />
                            <x-input id="cpf" name="cpf" type="text"
                                class="w-full mask-cpf" :value="old('cpf', $funcionario->cpf)" />
                        </div>
                        <div id="cnpj-field" class="hidden">
                            <x-input-label for="cnpj" :value="__('CNPJ')" />
                            <x-input id="cnpj" name="cnpj" type="text"
                                class="w-full mask-cnpj" :value="old('cnpj', $funcionario->cnpj)" />
                        </div>
                        <div id="rg-field">
                            <x-input-label for="rg" :value="__('RG')" />
                            <x-input id="rg" name="rg" type="text" class="w-full"
                                :value="old('rg', $funcionario->rg)" />
                        </div>
                        <div id="ie-field" class="hidden">
                            <x-input-label for="ie" :value="__('Inscrição Estadual')" />
                            <x-input id="ie" name="ie" type="text" class="w-full"
                                :value="old('ie', $funcionario->ie)" />
                        </div>
                        <div id="data-nascimento">
                            <x-input-label for="data_nascimento" :value="__('Data nascimento')" />
                            <x-input id="data_nascimento" name="data_nascimento" type="date"
                                class="w-full" :value="old('data_nascimento', optional($funcionario->data_nascimento)->format('Y-m-d'))" />
                        </div>
                        <div>
                            @php
                            @endphp
                            <x-input-label for="data_admissao" :value="__('Data admissão')" />
                            <x-input id="data_admissao" name="data_admissao" type="date"
                                class="w-full" :value="old('data_admissao', $funcionario->funcionario->data_admissao)" />
                        </div>

                        <h2 class="md:col-span-5">
                            {{ __('Contato') }}
                        </h2>
                        <div>
                            <x-input-label for="celular" :value="__('Celular')" />
                            <x-input id="celular" name="celular" type="text"
                                class="w-full mask-celular" :value="old('celular', $funcionario->celular)" />
                        </div>
                        <div>
                            <x-input-label for="telefone" :value="__('Telefone')" />
                            <x-input id="telefone" name="telefone" type="text"
                                class="w-full mask-telefone" :value="old('telefone', $funcionario->telefone)" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-input id="email" name="email" type="email" class="w-full"
                                :value="old('email', $funcionario->email)" />
                        </div>

                        <h2 class="md:col-span-5">
                            {{ __('Endereço') }}
                        </h2>

                        <div>
                            <x-input-label for="cep" :value="__('CEP')" />
                            <x-input id="cep" name="cep" type="text"
                                class="w-full mask-cep" :value="old('cep', $funcionario->cep)" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="logradouro" :value="__('Logradouro')" />
                            <x-input id="logradouro" name="logradouro" type="text" class="w-full"
                                :value="old('logradouro', $funcionario->logradouro)" />
                        </div>
                        <div>
                            <x-input-label for="numero" :value="__('Número')" />
                            <x-input id="numero" name="numero" type="text" class="w-full"
                                :value="old('numero', $funcionario->numero)" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="complemento" :value="__('Complemento')" />
                            <x-input id="complemento" name="complemento" type="text"
                                class="w-full" :value="old('complemento', $funcionario->complemento)" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="bairro" :value="__('Bairro')" />
                            <x-input id="bairro" name="bairro" type="text" class="w-full"
                                :value="old('bairro', $funcionario->bairro)" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="cidade" :value="__('Cidade')" />
                            <x-input id="cidade" name="cidade" type="text" class="w-full"
                                :value="old('cidade', $funcionario->cidade)" />
                        </div>
                        <div class="md:col-span-1">
                            <x-input-label for="uf" value="UF" />

                            <x-select-uf :value="$funcionario->uf"/>
                        </div>
                        <h2 class="md:col-span-5">
                            {{ __('Dados adicionais') }}
                        </h2>
                        <div class="md:col-span-5">
                            <x-input-label for="observacoes" :value="__('Observações')" />
                            <textarea name="observacoes" id="observacoes">{{ old('observacoes', $funcionario->funcionario->observacoes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <x-form-button :cancelUrl="route('funcionarios.index')" submitText="Salvar"/>

        </form>
    </div>
</x-app-layout>