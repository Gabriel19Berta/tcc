<?php

namespace App\Http\Controllers\Vendas;

use App\Http\Controllers\Controller;
use App\Models\Recebimento;
use App\Models\Venda;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    public function index(Request $request)
    {
        $vendas = Venda::query()
            ->with(['cliente.pessoa', 'funcionario.pessoa'])
            ->when($request->filled('codigo'), function ($query) use ($request) {
                $query->where('id', $request->codigo);
            })
            ->when(
                $request->filled('status') && $request->status !== 'todos',
                function ($query) use ($request) {
                    $query->where('status', $request->status);
                }
            )
            ->when($request->filled('data_inicio'), function ($query) use ($request) {
                $query->whereDate('data', '>=', $request->data_inicio);
            })
            ->when($request->filled('data_fim'), function ($query) use ($request) {
                $query->whereDate('data', '<=', $request->data_fim);
            })
            ->orderByDesc('id')
            ->paginate(15);

        return view('vendas.index', compact('vendas'));
    }

    /**
     * Consulta detalhada de uma venda
     */
    public function show(Venda $venda)
    {
        $venda->load([
            'cliente.pessoa',
            'funcionario.pessoa',
            'itens.produto',
            'recebimentos.formaPagamento',
        ]);

        // Separa os recebimentos já carregados por status (sem novas consultas).
        $recebimentos = $venda->recebimentos->groupBy('status');

        // Totalização dos pendentes, somada em centavos para evitar imprecisão.
        $pendentes = $recebimentos->get(Recebimento::STATUS_PENDENTE, collect());
        $totalPendentes = [
            'quantidade' => $pendentes->count(),
            'valor' => $pendentes->sum(fn (Recebimento $r) => (int) round((float) $r->valor * 100)) / 100.0,
        ];

        return view('vendas.show', compact('venda', 'recebimentos', 'totalPendentes'));
    }
}
