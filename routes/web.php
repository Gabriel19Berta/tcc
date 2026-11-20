<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\RecebimentoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\TipoProdutoController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Vendas\VendaController;
use App\Http\Controllers\Vendas\VendaPdvController;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| MÓDULOS DO SISTEMA
|--------------------------------------------------------------------------
| Disponíveis para qualquer usuário autenticado (administrador ou funcionário).
*/

Route::middleware('auth')->group(function () {

/* ALTERAR STATUS */
Route::patch('/status/{model}/{id}', [StatusController::class, 'toggle'])->name('status.toggle');
Route::get('/pessoas/buscar', [PessoaController::class, 'buscar'])->name('pessoas.buscar');

/* CLIENTES */
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
Route::post('/clientes/store', [ClienteController::class, 'store'])->name('clientes.store');
Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('clientes.show');
Route::get('/clientes/{id}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');
Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

/* FUNCIONÁRIOS */
Route::get('/funcionarios', [FuncionarioController::class, 'index'])->name('funcionarios.index');
Route::get('/funcionarios/create', [FuncionarioController::class, 'create'])->name('funcionarios.create');
Route::post('/funcionarios/store', [FuncionarioController::class, 'store'])->name('funcionarios.store');
Route::get('/funcionarios/{id}', [FuncionarioController::class, 'show'])->name('funcionarios.show');
Route::get('/funcionarios/{id}/edit', [FuncionarioController::class, 'edit'])->name('funcionarios.edit');
Route::put('/funcionarios/{id}', [FuncionarioController::class, 'update'])->name('funcionarios.update');
Route::delete('/funcionarios/{id}', [FuncionarioController::class, 'destroy'])->name('funcionarios.destroy');

/* MARCAS */
Route::get('/marcas', [MarcaController::class, 'index'])->name('marcas.index');
Route::get('/marcas/create', [MarcaController::class, 'create'])->name('marcas.create');
Route::post('/marcas/store', [MarcaController::class, 'store'])->name('marcas.store');
Route::get('/marcas/{id}', [MarcaController::class, 'show'])->name('marcas.show');
Route::get('/marcas/{id}/edit', [MarcaController::class, 'edit'])->name('marcas.edit');
Route::put('/marcas/{id}', [MarcaController::class, 'update'])->name('marcas.update');
Route::delete('/marcas/{id}', [MarcaController::class, 'destroy'])->name('marcas.destroy');

/* TIPO PRODUTO */
Route::get('/tipo-produtos/export', [TipoProdutoController::class, 'export'])->name('tipo-produtos.export');
Route::get('/tipo-produtos', [TipoProdutoController::class, 'index'])->name('tipo-produtos.index');
Route::get('/tipo-produtos/create', [TipoProdutoController::class, 'create'])->name('tipo-produtos.create');
Route::post('/tipo-produtos/store', [TipoProdutoController::class, 'store'])->name('tipo-produtos.store');
Route::get('/tipo-produtos/{id}', [TipoProdutoController::class, 'show'])->name('tipo-produtos.show');
Route::get('/tipo-produtos/{id}/edit', [TipoProdutoController::class, 'edit'])->name('tipo-produtos.edit');
Route::put('/tipo-produtos/{id}', [TipoProdutoController::class, 'update'])->name('tipo-produtos.update');
Route::delete('/tipo-produtos/{id}', [TipoProdutoController::class, 'destroy'])->name('tipo-produtos.destroy');

/* PRODUTO */
Route::get('/produtos', [ProdutoController::class, 'index'])->name('produtos.index');
Route::get('/produtos/create', [ProdutoController::class, 'create'])->name('produtos.create');
Route::post('/produtos/store', [ProdutoController::class, 'store'])->name('produtos.store');
Route::get('/produtos/{id}', [ProdutoController::class, 'show'])->name('produtos.show');
Route::get('/produtos/{id}/edit', [ProdutoController::class, 'edit'])->name('produtos.edit');
Route::put('/produtos/{id}', [ProdutoController::class, 'update'])->name('produtos.update');
Route::delete('/produtos/{id}', [ProdutoController::class, 'destroy'])->name('produtos.destroy');

/* VENDAS */
Route::get('/vendas', [VendaController::class, 'index'])->name('vendas.index');
Route::get('/vendas/{venda}', [VendaController::class, 'show'])->whereNumber('venda')->name('vendas.show');
Route::post('/vendas/{venda}/cancelar', [VendaController::class, 'cancelar'])->whereNumber('venda')->name('vendas.cancelar');
Route::delete('/vendas/{venda}', [VendaController::class, 'destroy'])->whereNumber('venda')->name('vendas.destroy');

/* PDV */
Route::get('/vendas/create', [VendaPdvController::class, 'create'])->name('vendas.create');
/* ADICIONAR PRODUTO À VENDA */
Route::post('/vendas/itens', [VendaPdvController::class, 'adicionarProduto'])->name('vendas.itens.store');
/* ABRIR VENDA EXISTENTE NO PDV */
Route::get('/vendas/{id}/edit', [VendaPdvController::class, 'edit'])->name('vendas.edit');

Route::delete('/vendas/{venda}/itens/{item}', [VendaPdvController::class, 'destroy'])->name('vendas.itens.destroy');
Route::patch('/vendas/{venda}/itens/{item}', [VendaPdvController::class, 'update'])->name('vendas.itens.update');
Route::patch('/vendas/{venda}/cliente', [VendaPdvController::class, 'atualizarCliente'])->name('vendas.cliente.update');
Route::patch('/vendas/{venda}/funcionario', [VendaPdvController::class, 'atualizarFuncionario'])->name('vendas.funcionario.update');
Route::patch('/vendas/{venda}/valores', [vendaPdvController::class, 'atualizarValores'])->name('vendas.valores.update');
Route::post('/vendas/{venda}/recebimentos', [VendaPdvController::class, 'store'])->name('vendas.recebimentos.store');
Route::post('/vendas/{venda}/finalizar', [VendaPdvController::class, 'finalizar'])->name('vendas.finalizar');
Route::delete('/vendas/{venda}/recebimentos/{recebimento}', [VendaPdvController::class, 'destroyRecebimento'])->name('vendas.recebimentos.destroy');

/* RECEBIMENTOS */
Route::get('/recebimentos', [RecebimentoController::class, 'index'])->name('recebimentos.index');

/* RELATÓRIOS */
Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
Route::get('relatorios/gerar', [RelatorioController::class, 'gerar'])->name('relatorios.gerar');

});

/*
|--------------------------------------------------------------------------
| GERENCIAMENTO DE USUÁRIOS
|--------------------------------------------------------------------------
| Exclusivo para administradores.
*/

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('usuarios', UsuarioController::class)
        ->except(['show']);
});