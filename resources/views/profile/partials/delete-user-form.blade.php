<section>
    <header>
        <h2>
            {{ __('Deletar Conta') }}
        </h2>

        <p class="mt-1 text-sm">
            {{ __('Depois que sua conta for excluída, todos os seus recursos e dados serão excluídos permanentemente. Antes de excluir sua conta, baixe todos os dados ou informações que deseja reter.') }}
        </p>
    </header>

    {{-- Ações --}}
    <div class="flex items-center justify-end mt-6">
        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            {{ __('Deletar Conta') }}
        </x-danger-button>
    </div>

    {{-- Modal de confirmação --}}
    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable
    >
        <form
            method="post"
            action="{{ route('profile.destroy') }}"
            class="p-6 w-full"
        >
            @csrf
            @method('delete')

            <h2>
                {{ __('Tem certeza que deseja excluir sua conta?') }}
            </h2>

            <p class="mt-1 text-sm">
                {{ __('Assim que sua conta for excluída, todos os seus recursos e dados serão excluídos permanentemente. Por favor, insira sua senha para confirmar que deseja excluir sua conta permanentemente.') }}
            </p>

            {{-- Senha --}}
            <div class="mt-6">
                <x-input-label
                    for="password"
                    :value="__('Senha')"
                    class="sr-only"
                />

                <x-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Senha') }}"
                />

                <x-input-error
                    class="mt-2"
                    :messages="$errors->userDeletion->get('password')"
                />
            </div>

            {{-- Ações do modal --}}
            <div class="flex items-center justify-end gap-4 mt-6">

                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button>
                    {{ __('Deletar Conta') }}
                </x-danger-button>

            </div>
        </form>
    </x-modal>
</section>
