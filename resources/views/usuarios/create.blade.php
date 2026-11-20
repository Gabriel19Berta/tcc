<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Cadastrar Usuário') }}
            </h1>
        </div>
    </x-slot>

    <div class="py-4">
        <form action="{{ route('usuarios.store') }}" method="POST">
            @csrf
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="name" :value="__('Nome')" class="required" />
                            <x-input id="name" name="name" type="text" class="w-full"
                                :value="old('name')" autocomplete="off" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="email" :value="__('E-mail')" class="required" />
                            <x-input id="email" name="email" type="email" class="w-full"
                                :value="old('email')" autocomplete="off" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="password" :value="__('Senha')" class="required" />
                            <x-input id="password" name="password" type="password" class="w-full"
                                autocomplete="new-password" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="password_confirmation" :value="__('Confirmação da senha')" class="required" />
                            <x-input id="password_confirmation" name="password_confirmation" type="password" class="w-full"
                                autocomplete="new-password" />
                        </div>

                        @include('usuarios.partials.campos-acesso', [
                            'adminAtual' => old('admin', '0'),
                            'funcionarioAtual' => old('funcionario_id'),
                            'bloquearAdmin' => false,
                        ])
                    </div>
                </div>
            </div>

            <x-form-button :cancelUrl="route('usuarios.index')" />
        </form>
    </div>
</x-app-layout>
