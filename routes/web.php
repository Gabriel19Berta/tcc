<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\TipoProdutoController;
use GuzzleHttp\Client;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

//PESSOAS
Route::patch('/pessoas/{id}/toggle-status', [PessoaController::class, 'toggleStatus'])->name('pessoas.toggleStatus');

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