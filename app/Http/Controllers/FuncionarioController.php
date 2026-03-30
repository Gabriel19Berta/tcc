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
        $query = Pessoa::with('funcionario')->wherehas('funcionario');

        if ($request->filled('codigo')) {
            $query->whereRelation('funcionario', 'id', $request->codigo);
        }

        $status = $request->input('status', '1');

        if ($status !== 'todos') {
            $query->where('status', $status);
        }

        if ($request->filled('nome')) {
            $busca = trim($request->nome);
            $termos = preg_split('/\s+/', $busca);

            $query->where(function ($q) use ($termos) {
                foreach ($termos as $termo) {
                    if (empty($termo)) continue;

                    $numero = preg_replace('/\D/', '', $termo);

                    $q->where(function ($sub) use ($termo, $numero) {
                        $sub->where('nome', 'like', "%{$termo}%");

                        // Busca CPF/CNPJ com ou sem máscara
                        if (!empty($numero)) {
                            $sub->orWhere('cpf', 'like', "%{$numero}%")
                                ->orWhere('cnpj', 'like', "%{$numero}%");
                        }
                    });
                }
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $funcionarios = $query->orderByDesc('id')->paginate(15)->withQueryString();

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
