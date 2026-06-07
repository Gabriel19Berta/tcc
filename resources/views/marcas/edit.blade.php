<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Alterar Marca') }}
            </h1>
        </div>
    </x-slot>
    <div class="py-4">
        <form action="{{ route('marcas.update', $marca->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-1">
                            <x-input-label for="status" :value="__('Status')" />
                            <div class="flex items-center gap-3 p-[6px] border border-gray-300 rounded-md shadow-sm">
                                <input type="hidden" name="status" value="0">
                                <x-text-input id="status" name="status" type="checkbox" value="1"
                                    class="rounded text-primary shadow-sm focus:ring-primary"
                                    :checked="old('status', $marca->status) == 1" 
                                />
                                <span>Ativo</span>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="nome" :value="__('Nome')" class="required" />
                            <x-text-input id="nome" name="nome" type="text" class="w-full"
                                :value="old('nome', $marca->nome)" />
                        </div>
                    </div>
                </div>
            </div>

            <x-form-button :cancelUrl="route('marcas.index')" submitText="Salvar"/>

        </form>
    </div>
</x-app-layout>