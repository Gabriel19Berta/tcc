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
        // Apenas funcionários que ainda não possuem conta de acesso.
        $funcionarios = Funcionario::with('pessoa')
            ->whereNull('user_id')
            ->get()
            ->sortBy('pessoa.nome');

        return view('usuarios.create', compact('funcionarios'));
    }

    /**
     * Salva um novo usuário.
     */
    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $dados = $request->validated();

        DB::transaction(function () use ($dados, $request) {
            $usuario = new User();

            $usuario->name = $dados['name'];
            $usuario->email = $dados['email'];
            $usuario->password = $dados['password'];
            $usuario->admin = $request->boolean('admin');

            $usuario->save();

            if (!empty($dados['funcionario_id'])) {
                $this->vincularFuncionario($usuario, (int) $dados['funcionario_id']);
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
        $usuario->load('funcionario.pessoa');

        // Funcionários sem conta + o funcionário já vinculado a este usuário.
        $funcionarios = Funcionario::with('pessoa')
            ->where(function ($query) use ($usuario) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $usuario->id);
            })
            ->get()
            ->sortBy('pessoa.nome');

        return view('usuarios.edit', compact('usuario', 'funcionarios'));
    }

    /**
     * Atualiza um usuário.
     */
    public function update(UpdateUsuarioRequest $request, User $usuario): RedirectResponse
    {
        $dados = $request->validated();
        $novoAdmin = $request->boolean('admin');

        // O administrador não pode remover o próprio privilégio.
        if ($usuario->is($request->user()) && !$novoAdmin) {
            throw ValidationException::withMessages([
                'admin' => 'Você não pode remover o seu próprio privilégio de administrador.',
            ]);
        }

        DB::transaction(function () use ($dados, $usuario, $novoAdmin) {
            // O sistema não pode ficar sem administrador.
            if ($usuario->admin && !$novoAdmin && $this->totalAdministradores() <= 1) {
                throw ValidationException::withMessages([
                    'admin' => 'Não é possível remover o privilégio do último administrador do sistema.',
                ]);
            }

            $usuario->name = $dados['name'];
            $usuario->email = $dados['email'];
            $usuario->admin = $novoAdmin;

            // Senha vazia = mantém a senha atual.
            if (!empty($dados['password'])) {
                $usuario->password = $dados['password'];
            }

            $usuario->save();

            $novoFuncionarioId = !empty($dados['funcionario_id'])
                ? (int) $dados['funcionario_id']
                : null;

            $funcionarioAtual = $usuario->funcionario()->lockForUpdate()->first();

            // Remove o vínculo anterior quando o funcionário foi trocado ou removido.
            if ($funcionarioAtual && $funcionarioAtual->id !== $novoFuncionarioId) {
                $funcionarioAtual->user()->dissociate();
                $funcionarioAtual->save();
            }

            // Cria o novo vínculo (ou mantém o atual).
            if ($novoFuncionarioId && $funcionarioAtual?->id !== $novoFuncionarioId) {
                $this->vincularFuncionario($usuario, $novoFuncionarioId);
            }
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    /**
     * Exclui um usuário.
     * O funcionário vinculado é preservado (FK com nullOnDelete).
     */
    public function destroy(User $usuario): RedirectResponse
    {
        // Impede que o administrador exclua a própria conta.
        if ($usuario->is(request()->user())) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', 'Você não pode excluir sua própria conta.');
        }

        $excluido = DB::transaction(function () use ($usuario) {
            // Impede a exclusão do último administrador.
            if ($usuario->admin && $this->totalAdministradores() <= 1) {
                return false;
            }

            $usuario->delete();

            return true;
        });

        if (!$excluido) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', 'Não é possível excluir o último administrador do sistema.');
        }

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }

    /**
     * Vincula um funcionário sem conta ao usuário informado.
     * Deve ser chamado dentro de uma transação.
     */
    private function vincularFuncionario(User $usuario, int $funcionarioId): void
    {
        $funcionario = Funcionario::whereKey($funcionarioId)
            ->lockForUpdate()
            ->firstOrFail();

        if ($funcionario->user_id !== null && (int) $funcionario->user_id !== $usuario->id) {
            throw ValidationException::withMessages([
                'funcionario_id' => 'Este funcionário já possui um usuário vinculado.',
            ]);
        }

        $funcionario->user()->associate($usuario);
        $funcionario->save();
    }

    /**
     * Conta os administradores bloqueando os registros até o fim da transação,
     * evitando que duas operações simultâneas removam os últimos administradores.
     */
    private function totalAdministradores(): int
    {
        return User::where('admin', true)->lockForUpdate()->count();
    }
}
