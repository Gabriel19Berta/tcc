<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PessoaRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Pessoa;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clientes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PessoaRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $pessoa = Pessoa::create($request->validated());
            
                $pessoa->cliente()->create(
                    $request->only('observacoes')
                );
            });

            return redirect()->route('clientes.index')->with('success', 'Cliente cadastro com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao cadastrar cliente. Tente novamente'); 
        }
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
}
