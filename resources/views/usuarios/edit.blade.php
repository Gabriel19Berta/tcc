<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Alterar Usuário') }}
            </h1>
        </div>
    </x-slot>

    <div class="py-4">
        <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="name" :value="__('Nome')" class="required" />
                            <x-input id="name" name="name" type="text" class="w-full"
                                :value="old('name', $usuario->name)" autocomplete="off" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="email" :value="__('E-mail')" class="required" />
                            <x-input id="email" name="email" type="email" class="w-full"
                                :value="old('email', $usuario->email)" autocomplete="off" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="password" :value="__('Nova senha')" />
                            <x-input id="password" name="password" type="password" class="w-full"
                                autocomplete="new-password" />
                            <p class="mt-1 text-xs text-gray-500">
                                Deixe em branco para manter a senha atual.
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="password_confirmation" :value="__('Confirmação da nova senha')" />
                            <x-input id="password_confirmation" name="password_confirmation" type="password" class="w-full"
                                autocomplete="new-password" />
                        </div>

                        @include('usuarios.partials.campos-acesso', [
                            'adminAtual' => old('admin', $usuario->admin ? '1' : '0'),
                            'funcionarioAtual' => old('funcionario_id', $usuario->funcionario?->id),
                            'bloquearAdmin' => $usuario->is(auth()->user()),
                        ])
                    </div>
                </div>
            </div>

            <x-form-button :cancelUrl="route('usuarios.index')" submitText="Salvar" />
        </form>
    </div>
</x-app-layout>
