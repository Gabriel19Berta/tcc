<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\Produto;
use App\Models\TipoProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProdutoRequest;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Produto::query()->with('marca', 'tipoProduto');

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

        $produtos = $query->OrderByDesc('id')->paginate(15);

        return view('produtos.index', compact('produtos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $marcas = Marca::all();
        $tipo_produtos = TipoProduto::all();

        return view('produtos.create', compact('marcas', 'tipo_produtos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProdutoRequest $request)
    {
        try {
            DB::transaction(function() use ($request) {
                Produto::create($request->validated());
            });
                        
            return redirect()->route('produtos.index')->with('success',  'Produto cadastrada com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao cadastrar produto. Tente novamente!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        $produto = Produto::with('marca', 'tipoProduto')->findOrFail($id);

        return view('produtos.show', compact('produto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $id)
    {
        $produto = Produto::with('marca', 'tipoProduto')->findOrFail($id);
        $marcas = Marca::all();
        $tipo_produtos = TipoProduto::all();

        return view('produtos.edit', compact('produto', 'marcas', 'tipo_produtos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProdutoRequest $request, String $id)
    {
        try {
            DB::transaction(function() use ($request, $id) {
                $produto = Produto::findOrFail($id);

                $produto->update($request->validated());
            });
                        
            return redirect()->route('produtos.index')->with('success',  'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar produto. Tente novamente!' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        try {
            $produto = Produto::findOrFail($id);
            $produto->delete();

            return redirect()->route('produtos.index')
                ->with('success', 'Produto excluída com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao excluir produto. Tente novamente!');
        }
    }
}
