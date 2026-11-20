<?php

use App\Models\Funcionario;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function criarAdmin(array $atributos = []): User
{
    $usuario = User::factory()->create($atributos);
    $usuario->forceFill(['admin' => true])->save();

    return $usuario;
}

function criarFuncionario(?User $usuario = null): Funcionario
{
    $pessoa = Pessoa::create(['nome' => 'Funcionário Teste', 'tipo' => 'f']);

    return Funcionario::create([
        'pessoa_id' => $pessoa->id,
        'user_id' => $usuario?->id,
    ]);
}

function dadosUsuario(array $sobrescrever = []): array
{
    return array_merge([
        'name' => 'Novo Usuário',
        'email' => 'novo@example.com',
        'password' => 'senha-segura-123',
        'password_confirmation' => 'senha-segura-123',
        'admin' => '0',
        'funcionario_id' => '',
    ], $sobrescrever);
}

/*
|--------------------------------------------------------------------------
| Cenário 1 — Administrador
|--------------------------------------------------------------------------
*/

test('administrador acessa listagem, cadastro e edição', function () {
    $admin = criarAdmin();
    $outro = User::factory()->create();
    criarFuncionario($outro);

    $this->actingAs($admin)->get(route('usuarios.index'))
        ->assertOk()
        ->assertSee($outro->email)
        ->assertSee('Funcionário Teste');

    $this->actingAs($admin)->get(route('usuarios.create'))->assertOk();
    $this->actingAs($admin)->get(route('usuarios.edit', $outro))->assertOk();
});

test('administrador cadastra usuário sem funcionário com senha criptografada', function () {
    $admin = criarAdmin();

    $this->actingAs($admin)
        ->post(route('usuarios.store'), dadosUsuario())
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('usuarios.index'));

    $usuario = User::where('email', 'novo@example.com')->first();

    expect($usuario)->not->toBeNull()
        ->and($usuario->admin)->toBeFalse()
        ->and($usuario->password)->not->toBe('senha-segura-123')
        ->and(Hash::check('senha-segura-123', $usuario->password))->toBeTrue()
        ->and($usuario->funcionario)->toBeNull();
});

test('administrador cadastra usuário administrador vinculado a funcionário', function () {
    $admin = criarAdmin();
    $funcionario = criarFuncionario();

    $this->actingAs($admin)
        ->post(route('usuarios.store'), dadosUsuario([
            'admin' => '1',
            'funcionario_id' => $funcionario->id,
        ]))
        ->assertSessionHasNoErrors();

    $usuario = User::where('email', 'novo@example.com')->first();

    expect($usuario->admin)->toBeTrue()
        ->and($funcionario->fresh()->user_id)->toBe($usuario->id);
});

test('administrador edita usuário, troca e remove funcionário e altera privilégio', function () {
    $admin = criarAdmin();
    $usuario = User::factory()->create();
    $funcA = criarFuncionario($usuario);
    $funcB = criarFuncionario();

    // Troca o funcionário e promove a administrador.
    $this->actingAs($admin)
        ->put(route('usuarios.update', $usuario), dadosUsuario([
            'email' => $usuario->email,
            'password' => '',
            'password_confirmation' => '',
            'admin' => '1',
            'funcionario_id' => $funcB->id,
        ]))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('usuarios.index'));

    expect($usuario->fresh()->admin)->toBeTrue()
        ->and($funcA->fresh()->user_id)->toBeNull()
        ->and($funcB->fresh()->user_id)->toBe($usuario->id);

    // Remove o vínculo e rebaixa para funcionário.
    $this->actingAs($admin)
        ->put(route('usuarios.update', $usuario), dadosUsuario([
            'email' => $usuario->email,
            'password' => '',
            'password_confirmation' => '',
            'admin' => '0',
            'funcionario_id' => '',
        ]))
        ->assertSessionHasNoErrors();

    expect($usuario->fresh()->admin)->toBeFalse()
        ->and($funcB->fresh()->user_id)->toBeNull();
});

test('administrador exclui outra conta e o funcionário é preservado', function () {
    $admin = criarAdmin();
    $usuario = User::factory()->create();
    $funcionario = criarFuncionario($usuario);

    $this->actingAs($admin)
        ->delete(route('usuarios.destroy', $usuario))
        ->assertRedirect(route('usuarios.index'))
        ->assertSessionHas('success');

    expect(User::find($usuario->id))->toBeNull()
        ->and($funcionario->fresh())->not->toBeNull()
        ->and($funcionario->fresh()->user_id)->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Cenário 2 — Funcionário
|--------------------------------------------------------------------------
*/

test('funcionário acessa os módulos normais do sistema', function () {
    $usuario = User::factory()->create();

    foreach (['dashboard', 'clientes.index', 'funcionarios.index', 'produtos.index',
        'marcas.index', 'tipo-produtos.index', 'vendas.index', 'relatorios.index', 'profile.edit'] as $rota) {
        $this->actingAs($usuario)->get(route($rota))->assertOk();
    }

    // PDV
    $this->actingAs($usuario)->get(route('vendas.create'))->assertSuccessful();
});

test('funcionário não acessa nenhuma rota de gerenciamento de usuários', function () {
    $usuario = User::factory()->create();
    $outro = User::factory()->create();

    $this->actingAs($usuario)->get(route('usuarios.index'))->assertForbidden();
    $this->actingAs($usuario)->get(route('usuarios.create'))->assertForbidden();
    $this->actingAs($usuario)->post(route('usuarios.store'), dadosUsuario())->assertForbidden();
    $this->actingAs($usuario)->get(route('usuarios.edit', $outro))->assertForbidden();
    $this->actingAs($usuario)->put(route('usuarios.update', $usuario), dadosUsuario(['admin' => '1']))->assertForbidden();
    $this->actingAs($usuario)->delete(route('usuarios.destroy', $outro))->assertForbidden();

    expect($usuario->fresh()->admin)->toBeFalse()
        ->and(User::find($outro->id))->not->toBeNull();
});

test('funcionário não vê o link de usuários no menu', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee(route('usuarios.index'));

    $this->actingAs(criarAdmin())->get(route('dashboard'))
        ->assertSee(route('usuarios.index'));
});

test('funcionário não consegue se promover pelo perfil', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->patch(route('profile.update'), [
            'name' => 'Nome Alterado',
            'email' => 'alterado@example.com',
            'admin' => '1',
        ])
        ->assertSessionHasNoErrors();

    $usuario->refresh();

    expect($usuario->name)->toBe('Nome Alterado')
        ->and($usuario->email)->toBe('alterado@example.com')
        ->and($usuario->admin)->toBeFalse();
});

test('visitante não autenticado é redirecionado para o login', function () {
    $this->get(route('usuarios.index'))->assertRedirect(route('login'));
    $this->get(route('clientes.index'))->assertRedirect(route('login'));
    $this->get(route('vendas.create'))->assertRedirect(route('login'));
});

test('não existe rota pública de cadastro de usuários', function () {
    $this->get('/register')->assertNotFound();
});

/*
|--------------------------------------------------------------------------
| Cenário 3 — Integridade
|--------------------------------------------------------------------------
*/

test('não é possível vincular o mesmo funcionário a duas contas', function () {
    $admin = criarAdmin();
    $dono = User::factory()->create();
    $outro = User::factory()->create();
    $funcionario = criarFuncionario($dono);

    $this->actingAs($admin)
        ->post(route('usuarios.store'), dadosUsuario(['funcionario_id' => $funcionario->id]))
        ->assertSessionHasErrors('funcionario_id');

    $this->actingAs($admin)
        ->put(route('usuarios.update', $outro), dadosUsuario([
            'email' => $outro->email,
            'password' => '',
            'password_confirmation' => '',
            'funcionario_id' => $funcionario->id,
        ]))
        ->assertSessionHasErrors('funcionario_id');

    expect($funcionario->fresh()->user_id)->toBe($dono->id)
        ->and(User::where('email', 'novo@example.com')->exists())->toBeFalse();
});

test('funcionário pode existir sem conta de acesso', function () {
    $funcionario = criarFuncionario();

    expect($funcionario->fresh()->user_id)->toBeNull();
});

test('administrador não pode excluir a própria conta', function () {
    $admin = criarAdmin();
    criarAdmin();

    $this->actingAs($admin)
        ->delete(route('usuarios.destroy', $admin))
        ->assertSessionHas('error');

    expect(User::find($admin->id))->not->toBeNull();
});

test('administrador não pode remover o próprio privilégio', function () {
    $admin = criarAdmin();
    criarAdmin();

    $this->actingAs($admin)
        ->put(route('usuarios.update', $admin), dadosUsuario([
            'email' => $admin->email,
            'password' => '',
            'password_confirmation' => '',
            'admin' => '0',
        ]))
        ->assertSessionHasErrors('admin');

    expect($admin->fresh()->admin)->toBeTrue();
});

test('não é possível excluir ou rebaixar o último administrador', function () {
    // Com um único administrador, só ele mesmo passa pelo middleware.
    $unicoAdmin = criarAdmin();
    $funcionario = User::factory()->create();

    $this->actingAs($unicoAdmin)
        ->put(route('usuarios.update', $unicoAdmin), dadosUsuario([
            'email' => $unicoAdmin->email,
            'password' => '',
            'password_confirmation' => '',
            'admin' => '0',
        ]))
        ->assertSessionHasErrors('admin');

    $this->actingAs($unicoAdmin)
        ->delete(route('usuarios.destroy', $unicoAdmin))
        ->assertSessionHas('error');

    // Um funcionário tentando rebaixar/excluir o último admin é barrado antes.
    $this->actingAs($funcionario)
        ->delete(route('usuarios.destroy', $unicoAdmin))
        ->assertForbidden();

    expect(User::where('admin', true)->count())->toBe(1);
});

test('edição sem nova senha mantém a senha anterior', function () {
    $admin = criarAdmin();
    $usuario = User::factory()->create();
    $hashAnterior = $usuario->password;

    $this->actingAs($admin)
        ->put(route('usuarios.update', $usuario), dadosUsuario([
            'email' => $usuario->email,
            'password' => '',
            'password_confirmation' => '',
        ]))
        ->assertSessionHasNoErrors();

    expect($usuario->fresh()->password)->toBe($hashAnterior)
        ->and(Hash::check('password', $usuario->fresh()->password))->toBeTrue();
});

test('edição com nova senha altera a senha', function () {
    $admin = criarAdmin();
    $usuario = User::factory()->create();

    $this->actingAs($admin)
        ->put(route('usuarios.update', $usuario), dadosUsuario([
            'email' => $usuario->email,
            'password' => 'nova-senha-123',
            'password_confirmation' => 'nova-senha-123',
        ]))
        ->assertSessionHasNoErrors();

    expect(Hash::check('nova-senha-123', $usuario->fresh()->password))->toBeTrue();
});

test('cadastro exige senha, confirmação e e-mail único', function () {
    $admin = criarAdmin();

    $this->actingAs($admin)
        ->post(route('usuarios.store'), dadosUsuario([
            'email' => $admin->email,
            'password' => '',
            'password_confirmation' => '',
            'admin' => 'x',
        ]))
        ->assertSessionHasErrors(['email', 'password', 'admin']);
});
