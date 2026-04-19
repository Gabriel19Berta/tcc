<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\MarcaRequest;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Marca::query();

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

        $marcas = $query->OrderByDesc('id')->paginate(15);

        return view('marcas.index', compact('marcas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('marcas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MarcaRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Marca::create($request->validated());
            });

            return redirect()->route('marcas.index')->with('success',  'Marca cadastrada com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao cadastrar marca. Tente novamente');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        $marca = Marca::findOrFail($id);

        return view('marcas.show', compact('marca'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $id)
    {
        $marca = Marca::findOrFail($id);

        return view('marcas.edit', compact('marca'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MarcaRequest $request, String $id)
    {
        try {
            DB::transaction(function() use ($request, $id) {

                $marca = Marca::findOrFail($id);

                $marca->update($request->validated());
            });

            return redirect()->route('marcas.index')->with('success', 'Marca atualizada com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar marca. Tente novamente!'); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        try {
            $marca = Marca::findOrFail($id);
            $marca->delete();

            return redirect()->route('marcas.index')
                ->with('success', 'Marca excluída com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir marca!');
        }
    }
}
