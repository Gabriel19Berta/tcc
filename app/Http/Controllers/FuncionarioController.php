<?php

namespace App\Http\Controllers;

use App\Http\Requests\PessoaRequest;
use App\Models\Funcionario;
use App\Models\Pessoa;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FuncionarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $funcionarios = Pessoa::query()
        ->with('funcionario')
        ->wherehas('funcionario')
        ->filtroCodigo($request->codigo, 'funcionario')
        ->filtroStatus($request->input('status', '1'))
        ->filtroNome($request->nome)
        ->filtroTipo($request->tipo)
        ->OrderByDesc('id')
        ->paginate(15);

        return view('funcionarios.index', compact('funcionarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('funcionarios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PessoaRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $pessoa = Pessoa::create($request->validated());
            
                $pessoa->funcionario()->create(
                    $request->only('observacoes', 'data_admissao')
                );
            });

            return redirect()->route('funcionarios.index')->with('success', 'Funcionário cadastro com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao cadastrar funcionário. Tente novamente'); 
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $funcionario = Pessoa::with('funcionario')->findOrFail($id);

        return view('funcionarios.show', compact('funcionario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $funcionario = Pessoa::with('funcionario')->findOrFail($id);

        return view('funcionarios.edit', compact('funcionario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PessoaRequest $request, string $id)
    {
        try {
            DB::transaction(function() use ($request, $id) {

                $pessoa = Pessoa::findOrFail($id);

                $pessoa->update($request->validated());

                if ($pessoa->funcionario) {
                    $pessoa->funcionario->update(
                        $request->only('observacoes', 'data_admissao')
                    );
                }
            });

            return redirect()->route('funcionarios.index')->with('success', 'Funcionário atualizado com sucesso!');
        } catch (\Exception $th) {
            return back()->withInput()->with('error', 'Erro ao atualizar funcionário. Tente novamente'); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $funcionario = Funcionario::findOrFail($id);
            $funcionario->delete();

            return redirect()->route('funcionarios.index')
                    ->with('success', 'Funcionário excluído com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir funcionário');
        }
    }
}
