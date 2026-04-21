<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoProdutoRequest;
use App\Models\TipoProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipoProduto::query();

        if ($request->filled('codigo')) {
            $query->where('id', $request->codigo);
        }

        $status = $request->input('status', '1');

        if ($status !== 'todos') {
            $query->where('status', $status);
        }

        if ($request->filled('nome')) {
            $query->where('nome', 'LIKE', '%' . $request->nome . '%');
        }

        $tipoProdutos = $query->OrderByDesc('id')->paginate(15);

        return view('tipo-produtos.index', compact('tipoProdutos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipo-produtos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TipoProdutoRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                TipoProduto::create($request->validated());
            });

            return redirect()->route('tipo-produtos.index')->with('success',  'Tipo produto cadastrado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao cadastrar tipo produto. Tente novamente!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        $tipoProduto = TipoProduto::findOrFail($id);

        return view('tipo-produtos.show', compact('tipoProduto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $id)
    {
        $tipoProduto = TipoProduto::findOrFail($id);

        return view('tipo-produtos.edit', compact('tipoProduto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TipoProdutoRequest $request, String $id)
    {
        try {
            DB::transaction(function() use ($request, $id) {

                $tipoProduto = TipoProduto::findOrFail($id);

                $tipoProduto->update($request->validated());
            });

            return redirect()->route('tipo-produtos.index')->with('success', 'Tipo produto atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar tipo produto. Tente novamente!'); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        try {
            $marca = TipoProduto::findOrFail($id);
            $marca->delete();

            return redirect()->route('tipo-produtos.index')
                ->with('success', 'Tipo produto excluído com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir tipo produto!');
        }
    }
}
