<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PessoaRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Pessoa;
use App\Models\Cliente;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pessoa::with('cliente')->wherehas('cliente');

        if ($request->filled('codigo')) {
            $query->whereRelation('cliente', 'id', $request->codigo);
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

        $clientes = $query->OrderByDesc('id')->paginate(15)->withQueryString();

        return view('clientes.index', compact('clientes'));
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

            return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao cadastrar cliente. Tente novamente'); 
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cliente = Pessoa::with('cliente')->findOrFail($id);

        return view('clientes.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cliente = Pessoa::with('cliente')->findOrFail($id);

        return view('clientes.edit', compact('cliente'));
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

                if ($pessoa->cliente) {
                    $pessoa->cliente->update(
                        $request->only('observacoes')
                    );
                }
            });

            return redirect()->route('clientes.index')->with('success', 'Cliente atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar cliente. Tente novamente!'); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->delete();

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente excluído com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir cliente!');
        }
    }
}
