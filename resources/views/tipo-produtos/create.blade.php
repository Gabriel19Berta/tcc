<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Cadastrar Tipo Produto') }}
            </h1>
        </div>
    </x-slot>

    <div class="py-4">
        <form action="{{ route('tipo-produtos.store') }}" method="POST">
            @csrf
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="nome" :value="__('Nome')" class="required" />
                            <x-input id="nome" name="nome" type="text" class="w-full"
                                :value="old('nome')" />
                        </div>
                    </div>
                </div>
            </div>

            <x-form-button :cancelUrl="route('tipo-produtos.index')" />

        </form>
    </div>
</x-app-layout>