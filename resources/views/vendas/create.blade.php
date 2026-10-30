<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nova venda
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <p class="mb-4">
                    Deseja iniciar uma nova venda?
                </p>

                <form method="POST" action="{{ route('vendas.store') }}">
                    @csrf

                    <button
                        type="submit"
                        class="px-4 py-2 rounded-md bg-primary text-white"
                    >
                        Iniciar venda
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
