<?php

namespace App\Http\Controllers\Vendas;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\Recebimento;
use App\Models\Venda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->when($request->filled('cliente'), function ($query) use ($request) {
                $query->whereHas('cliente.pessoa', fn ($pessoa) => $pessoa->filtroBusca($request->cliente));
            })
            ->when($request->filled('funcionario'), function ($query) use ($request) {
                $query->whereHas('funcionario.pessoa', fn ($pessoa) => $pessoa->filtroBusca($request->funcionario));
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

    /**
     * Cancela a venda
     *
     * - Bloqueado se houver qualquer recebimento pago (devem ser estornados antes).
     * - Recebimentos pendentes passam para "cancelado".
     * - Se o estoque já foi baixado na finalização, as quantidades são devolvidas.
     */
    public function cancelar(Venda $venda): RedirectResponse
    {
        $erro = DB::transaction(function () use ($venda) {
            // Bloqueia a venda e revalida o status dentro da transação.
            $venda = Venda::query()->lockForUpdate()->findOrFail($venda->id);

            if ($venda->status === Venda::STATUS_CANCELADA) {
                return 'Esta venda já está cancelada.';
            }

            if (!$venda->podeSerCancelada()) {
                return 'Esta venda não pode ser cancelada.';
            }

            // Verifica os recebimentos diretamente nos registros (não pelo status da venda).
            $recebimentos = $venda->recebimentos()->lockForUpdate()->get();

            // Pagos ou pendentes com pagamento parcial já registrado: dinheiro recebido.
            $temPagamento = $recebimentos->contains('status', Recebimento::STATUS_PAGO)
                || $venda->recebimentos()->whereHas('pagamentos')->exists();

            if ($temPagamento) {
                return 'Não é possível cancelar esta venda porque existem recebimentos pagos ou com pagamentos registrados. '
                    . 'Realize o estorno dos recebimentos antes de cancelar a venda.';
            }

            // Devolve ao estoque o que foi baixado na finalização.
            if ($venda->estoqueFoiBaixado()) {
                $this->devolverEstoque($venda);
            }

            // Cancela os pendentes, mantendo valores e datas.
            $venda->recebimentos()
                ->where('status', Recebimento::STATUS_PENDENTE)
                ->update(['status' => Recebimento::STATUS_CANCELADO]);

            $venda->update(['status' => Venda::STATUS_CANCELADA]);

            return null;
        });

        // Volta para a tela de origem (detalhes ou listagem com seus filtros).
        return redirect()
            ->back(fallback: route('vendas.show', $venda->id))
            ->with($erro ? 'error' : 'success', $erro ?? 'Venda cancelada com sucesso.');
    }

    /**
     * Exclui definitivamente uma venda cancelada.
     */
    public function destroy(Venda $venda): RedirectResponse
    {
        $erro = DB::transaction(function () use ($venda) {
            // Bloqueia a venda e revalida o status dentro da transação.
            $venda = Venda::query()->lockForUpdate()->findOrFail($venda->id);

            if (!$venda->podeSerExcluida()) {
                return 'Somente vendas canceladas podem ser excluídas. Cancele a venda antes de excluí-la.';
            }

            $recebimentos = $venda->recebimentos()->lockForUpdate()->get();

            $temMovimentacao = $recebimentos->contains(
                fn (Recebimento $recebimento) => $recebimento->status === Recebimento::STATUS_PAGO
                    || $recebimento->data_pagamento !== null
            ) || $venda->recebimentos()->whereHas('pagamentos')->exists();

            if ($temMovimentacao) {
                return 'Não é possível excluir esta venda porque existem recebimentos vinculados com '
                    . 'movimentação financeira. Trate esses recebimentos antes de excluir a venda.';
            }

            $venda->recebimentos()->delete();
            $venda->itens()->delete();
            $venda->delete();

            return null;
        });

        return redirect()
            ->back(fallback: route('vendas.index'))
            ->with($erro ? 'error' : 'success', $erro ?? 'Venda excluída com sucesso.');
    }

    /**
     * Soma as quantidades vendidas de cada produto (pelos itens da venda)
     * e devolve ao estoque dos produtos que controlam estoque — o mesmo
     * critério usado na baixa feita em VendaPdvController::finalizar().
     */
    private function devolverEstoque(Venda $venda): void
    {
        $itensPorProduto = $venda->itens()->get()->groupBy('produto_id');

        foreach ($itensPorProduto as $produtoId => $itens) {
            $produto = Produto::query()->lockForUpdate()->findOrFail($produtoId);

            if (!$produto->controla_estoque) {
                continue;
            }

            // Soma em centésimos, como na finalização, para evitar imprecisão.
            $estoqueEmCentesimos = (int) round((float) $produto->quantidade * 100);
            $vendidoEmCentesimos = (int) round((float) $itens->sum('quantidade') * 100);

            $produto->update([
                'quantidade' => ($estoqueEmCentesimos + $vendidoEmCentesimos) / 100,
            ]);
        }
    }
}
