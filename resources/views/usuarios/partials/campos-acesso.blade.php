{{--
    Campos compartilhados entre cadastro e edição de usuário:
    tipo de acesso e funcionário vinculado.

    Variáveis: $funcionarios, $adminAtual ('0'|'1'), $funcionarioAtual (id|null), $bloquearAdmin (bool)
--}}

<div class="md:col-span-2">
    <x-input-label :value="__('Tipo de acesso')" class="required" />
    <div class="contain-check">
        <label for="admin_nao" class="flex items-center gap-2 cursor-pointer">
            <input id="admin_nao" name="admin" type="radio" value="0"
                class="text-primary focus:ring-primary"
                @checked((string) $adminAtual === '0')
                @disabled($bloquearAdmin) />
            <span>Funcionário</span>
        </label>

        <label for="admin_sim" class="flex items-center gap-2 cursor-pointer">
            <input id="admin_sim" name="admin" type="radio" value="1"
                class="text-primary focus:ring-primary"
                @checked((string) $adminAtual === '1') />
            <span>Administrador</span>
        </label>
    </div>

    @if ($bloquearAdmin)
        <p class="mt-1 text-xs text-gray-500">
            Você não pode remover o seu próprio privilégio de administrador.
        </p>
    @else
        <p class="mt-1 text-xs text-gray-500">
            Administradores também podem gerenciar as contas de usuário.
        </p>
    @endif

    @error('admin')
        <p class="mt-1 text-sm text-danger-500 input-error">{{ $message }}</p>
    @enderror
</div>

<div class="md:col-span-2">
    <x-input-label for="funcionario_id" :value="__('Funcionário vinculado (opcional)')" />
    <select id="funcionario_id" name="funcionario_id" class="select2 w-full">
        <option value="">Nenhum funcionário</option>
        @foreach ($funcionarios as $funcionario)
            <option value="{{ $funcionario->id }}" @selected((string) $funcionarioAtual === (string) $funcionario->id)>
                {{ $funcionario->pessoa?->nome ?? 'Funcionário #' . $funcionario->id }}
            </option>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-gray-500">
        O vínculo é opcional. Somente funcionários sem conta de acesso são listados.
    </p>

    @error('funcionario_id')
        <p class="mt-1 text-sm text-danger-500 input-error">{{ $message }}</p>
    @enderror
</div>
