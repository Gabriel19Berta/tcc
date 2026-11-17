<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Venda - {{ $venda->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <p>
                    Venda criada com sucesso.
                </p>

                <p class="mt-2">
                    Código: {{ $venda->id }}
                </p>

                <p>
                    Status: {{ $venda->status }}
                </p>

                <p>
                    Total: R$ {{ number_format($venda->valor_total, 2, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
