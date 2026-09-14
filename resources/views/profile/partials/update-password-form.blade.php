<section>
    <header>
        <h2>
            {{ __('Alterar Senha') }}
        </h2>

        <p class="mt-1 text-sm">
            {{ __('Certifique-se de que sua conta esteja usando uma senha longa e aleatória para permanecer segura.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 w-full">
        @csrf
        @method('put')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Senha atual --}}
            <div>
                <x-input-label
                    for="update_password_current_password"
                    :value="__('Senha atual')"
                />

                <x-input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />

                <x-input-error
                    class="mt-2"
                    :messages="$errors->updatePassword->get('current_password')"
                />
            </div>

            {{-- Nova senha --}}
            <div>
                <x-input-label
                    for="update_password_password"
                    :value="__('Nova Senha')"
                />

                <x-input
                    id="update_password_password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <x-input-error
                    class="mt-2"
                    :messages="$errors->updatePassword->get('password')"
                />
            </div>

            {{-- Confirmação da senha --}}
            <div>
                <x-input-label
                    for="update_password_password_confirmation"
                    :value="__('Confirmação Senha')"
                />

                <x-input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <x-input-error
                    class="mt-2"
                    :messages="$errors->updatePassword->get('password_confirmation')"
                />
            </div>

        </div>

        {{-- Ações --}}
        <div class="flex items-center justify-end gap-4 mt-6">

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-primary"
                >
                    {{ __('Salvo.') }}
                </p>
            @endif

            <x-primary-button>
                {{ __('Salvar') }}
            </x-primary-button>

        </div>
    </form>
</section>
