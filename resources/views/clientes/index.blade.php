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
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Código
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Nome
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($clientes as $cliente)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $cliente->id }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $cliente->nome }}
                                        </td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
