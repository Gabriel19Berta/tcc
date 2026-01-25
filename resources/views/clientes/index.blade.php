<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1 class="inline text-primary font-semibold text-2xl leading-tight">
                {{ __('Clientes') }}
            </h1>
            <x-action-link href="{{ route('clientes.create') }}">
                Cadastrar
            </x-action-link>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
