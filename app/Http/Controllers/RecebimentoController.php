<?php

namespace App\Http\Controllers;

use App\Models\FormaPagamento;
use App\Models\Recebimento;
use Illuminate\Http\Request;

class RecebimentoController extends Controller
{
    /**
     * Listagem de recebimentos (mais recentes primeiro), com filtros.
     */
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'codigo' => ['nullable', 'integer', 'min:1'],
            'venda' => ['nullable', 'integer', 'min:1'],
            'cliente' => ['nullable', 'string', 'max:100'],
            'forma_pagamento_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:todos,' . implode(',', Recebimento::STATUS)],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
        ], [
            'data_fim.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ]);

        $recebimentos = Recebimento::query()
            ->with(['venda.cliente.pessoa', 'formaPagamento'])
            ->when($filtros['codigo'] ?? null, function ($query, $codigo) {
                $query->whereKey($codigo);
            })
            ->when($filtros['venda'] ?? null, function ($query, $vendaId) {
                $query->where('venda_id', $vendaId);
            })
            ->when($filtros['cliente'] ?? null, function ($query, $cliente) {
                $query->whereHas('venda.cliente.pessoa', fn ($pessoa) => $pessoa->filtroBusca($cliente));
            })
            ->when($filtros['forma_pagamento_id'] ?? null, function ($query, $formaId) {
                $query->where('forma_pagamento_id', $formaId);
            })
            ->when(($filtros['status'] ?? 'todos') !== 'todos', function ($query) use ($filtros) {
                $query->where('status', $filtros['status']);
            })
            ->when($filtros['data_inicio'] ?? null, function ($query, $data) {
                $query->whereDate('data_vencimento', '>=', $data);
            })
            ->when($filtros['data_fim'] ?? null, function ($query, $data) {
                $query->whereDate('data_vencimento', '<=', $data);
            })
            ->orderByDesc('id')
            ->paginate(15);

        $formasPagamento = FormaPagamento::query()->orderBy('nome')->get(['id', 'nome']);

        return view('recebimentos.index', compact('recebimentos', 'formasPagamento'));
    }
}
