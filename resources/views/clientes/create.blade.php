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

                        <div class="grid grid-cols-5 md:grid-cols-5 gap-4">
                            <div class="col-span-2">
                                <x-input-label for="nome" :value="__('Nome')" />
                                <x-text-input id="nome" name="nome" type="text" class="block mt-1 w-full"
                                    :value="old('nome')" required />
                            </div>
                            <div class="col-span-1">
                                <x-input-label for="tipo" :value="__('Tipo')" />

                                <div class="border border-gray-300 rounded-md mt-1 p-[6px] flex gap-4">

                                    <label for="fisico" class="flex items-center gap-2 cursor-pointer">
                                        <x-text-input id="fisico" name="tipo" type="radio" value="F"
                                            :checked="old('tipo') === 'F'" required />
                                        <span>Física</span>
                                    </label>

                                    <label for="juridica" class="flex items-center gap-2 cursor-pointer">
                                        <x-text-input id="juridica" name="tipo" type="radio" value="J"
                                            :checked="old('tipo') === 'J'" />
                                        <span>Jurídica</span>
                                    </label>

                                </div>
                            </div>
                            <div class="col-span-1">
                                <x-input-label for="cpf" :value="__('CPF')" />
                                <x-text-input id="cpf" name="cpf" type="text" class="block mt-1 w-full"
                                    :value="old('cpf')" required />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
