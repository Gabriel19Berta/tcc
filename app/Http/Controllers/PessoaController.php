<?php

namespace App\Http\Controllers;

use App\Http\Requests\PessoaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pessoa;

class PessoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function buscar(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'tipo' => ['required', 'in:cliente,funcionario'],
            'termo' => ['nullable', 'string', 'max:100'],
        ]);

        $pessoas = Pessoa::query()
            ->whereHas($dados['tipo'])
            ->filtroNome($dados['termo'] ?? null)
            ->orderBy('nome')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $pessoas->map(fn (Pessoa $pessoa) => [
                'id' => "pessoa:{$pessoa->id}",
                'text' => $pessoa->descricaoBusca(),
            ]),
        ]);
    }
}
