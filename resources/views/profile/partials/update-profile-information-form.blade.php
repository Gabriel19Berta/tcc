<section>
    <header>
        <h2>
            {{ __('Informação do Perfil') }}
        </h2>

        <p class="mt-1 text-sm">
            {{ __("Atualize as informações do perfil da sua conta e o endereço de e-mail.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 w-full">
        @csrf
        @method('patch')

        {{-- Campos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Nome --}}
            <div>
                <x-input-label for="name" :value="__('Nome')" />

                <x-input
                    id="name"
                    name="name"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('name', $user->name)"
                    required
                    autofocus
                    autocomplete="name"
                />

                <x-input-error
                    class="mt-2"
                    :messages="$errors->get('name')"
                />
            </div>

            {{-- Email --}}
            <div>
                <x-input-label for="email" :value="__('Email')" />

                <x-input
                    id="email"
                    name="email"
                    type="email"
                    class="mt-1 block w-full"
                    :value="old('email', $user->email)"
                    required
                    autocomplete="username"
                />

                <x-input-error
                    class="mt-2"
                    :messages="$errors->get('email')"
                />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2">
                            {{ __('Seu endereço de e-mail não foi verificado.') }}

                            <button
                                form="send-verification"
                                class="underline text-sm hover:text-primary rounded-md"
                            >
                                {{ __('Clique aqui para reenviar o e-mail de verificação.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-primary">
                                {{ __('Um novo link de verificação foi enviado para o seu endereço de e-mail.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

        </div>

        {{-- Ações --}}
        <div class="flex items-center justify-end gap-4 mt-6">

            @if (session('status') === 'profile-updated')
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
