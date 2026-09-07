<?php

namespace App\Http\Controllers;

use Exception;

class StatusController extends Controller
{
    public function toggle($id, $model)
    {
        try {
            $modelClass = match ($model) {
                'pessoas' => \App\Models\Pessoa::class,
                'marcas' => \App\Models\Marca::class,
                'tipoProdutos' => \App\Models\TipoProduto::class,
                'produtos' => \App\Models\Produto::class,
            };

            $registro = $modelClass::findOrFail($id);

            $registro->status = !$registro->status;
            $registro->save();

            return back()->with('success', 'Status alterado com sucesso!');
        } catch(Exception $e) {
            return back()->with('error', 'Status não pode ser alterado! Tente novamente');
        }
    }
}
