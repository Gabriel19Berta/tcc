<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h1>
                {{ __('Usuários') }}
            </h1>
            <x-action-link href="{{ route('usuarios.create') }}">
                Cadastrar
            </x-action-link>
        </div>
    </x-slot>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th class="text-center">Tipo de acesso</th>
                <th>Funcionário vinculado</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($usuarios as $usuario)
                <tr>
                    <td>
                        {{ $usuario->name }}
                        @if ($usuario->is(auth()->user()))
                            <span class="text-xs text-gray-500">(você)</span>
                        @endif
                    </td>
                    <td>
                        {{ $usuario->email }}
                    </td>
                    <td class="w-40 text-center">
                        @if ($usuario->admin)
                            <span class="inline-block px-2 py-1 rounded-md text-xs font-semibold text-white bg-primary">
                                Administrador
                            </span>
                        @else
                            <span class="inline-block px-2 py-1 rounded-md text-xs font-semibold text-white bg-beige-600">
                                Funcionário
                            </span>
                        @endif
                    </td>
                    <td>
                        {{ $usuario->funcionario?->pessoa?->nome }}
                    </td>
                    <td class="w-2">
                        <div class="flex gap-2 justify-center">
                            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-warning" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            @unless ($usuario->is(auth()->user()))
                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="form-delete">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger" title="Excluir">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endunless
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-gray-500">
                        Nenhum usuário cadastrado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $usuarios->links() }}
</x-app-layout>
