<x-app-layout>
    <x-slot name="header">
        <h1 class="text-primary font-semibold text-2xl leading-tight">
            {{ __('Dashboard') }}
        </h1>
    </x-slot>

    <div class="py-8">
        <div class="text-primary mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Em breve!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
