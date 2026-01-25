<x-app-layout>
    <x-slot name="header">
        <h1 class="text-primary font-semibold text-2xl leading-tight">
            {{ __('Dashboard') }}
        </h1>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Você acessou seu sistema!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
