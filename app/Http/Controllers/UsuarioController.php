<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\Funcionario;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * Lista os usuários cadastrados.
     */
    public function index(): View
    {
        $usuarios = User::with('funcionario.pessoa')
            ->orderBy('name')
            ->paginate(10);

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Exibe o formulário de cadastro.
     */
    public function create(): View
    {
        $funcionarios = Funcionario::with('pessoa')
            ->whereNull('user_id')
            ->get();

        return view('usuarios.create', compact('funcionarios'));
    }

    /**
     * Salva um novo usuário.
     */
    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $dados = $request->validated();

        DB::transaction(function () use ($dados) {
            $usuario = new User();

            $usuario->name = $dados['name'];
            $usuario->email = $dados['email'];
            $usuario->password = $dados['password'];
            $usuario->admin = $dados['admin'];

            $usuario->save();

            if (!empty($dados['funcionario_id'])) {
                $funcionario = Funcionario::whereKey($dados['funcionario_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($funcionario->user_id !== null) {
                    throw ValidationException::withMessages([
                        'funcionario_id' => 'Este funcionário já possui um usuário vinculado.',
                    ]);
                }

                $funcionario->user()->associate($usuario);
                $funcionario->save();
            }
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuário cadastrado com sucesso.');
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(User $usuario): View
    {
        $funcionarios = Funcionario::with('pessoa')
            ->whereNull('user_id')
            ->orWhere('user_id', $usuario->id)
            ->get();

        return view('usuarios.edit', compact('usuario', 'funcionarios'));
    }

    /**
     * Atualiza um usuário.
     */
    public function update(
        UpdateUsuarioRequest $request,
        User $usuario
    ): RedirectResponse {
        $dados = $request->validated();

        DB::transaction(function () use ($dados, $usuario) {

            $usuario->name = $dados['name'];
            $usuario->email = $dados['email'];
            $usuario->admin = $dados['admin'];

            if (!empty($dados['password'])) {
                $usuario->password = $dados['password'];
            }

            $usuario->save();

            $funcionarioAtual = $usuario->funcionario()
                ->lockForUpdate()
                ->first();

            $novoFuncionarioId = $dados['funcionario_id'] ?? null;

            // Remove o vínculo anterior quando necessário.
            if (
                $funcionarioAtual &&
                (int) $funcionarioAtual->id !== (int) $novoFuncionarioId
            ) {
                $funcionarioAtual->user_id = null;
                $funcionarioAtual->save();
            }

            // Cria ou mantém o vínculo selecionado.
            if ($novoFuncionarioId) {
                $novoFuncionario = Funcionario::whereKey($novoFuncionarioId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $novoFuncionario->user_id !== null &&
                    $novoFuncionario->user_id !== $usuario->id
                ) {
                    throw ValidationException::withMessages([
                        'funcionario_id' => 'Este funcionário já possui um usuário vinculado.',
                    ]);
                }

                $novoFuncionario->user()->associate($usuario);
                $novoFuncionario->save();
            }
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    /**
     * Exclui um usuário.
     */
    public function destroy(User $usuario): RedirectResponse
    {
        // Impede que o administrador exclua a própria conta.
        if ($usuario->is(auth()->user())) {
            return back()->with(
                'error',
                'Você não pode excluir sua própria conta.'
            );
        }

        // Impede a exclusão do último administrador.
        if (
            $usuario->admin &&
            User::where('admin', true)->count() <= 1
        ) {
            return back()->with(
                'error',
                'Não é possível excluir o último administrador do sistema.'
            );
        }

        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}
