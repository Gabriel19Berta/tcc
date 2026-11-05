<?php

namespace App\Http\Controllers\Vendas;

use App\Http\Controllers\Controller;
use App\Models\ItemVenda;
use App\Models\Produto;
use App\Models\Venda;
use Illuminate\View\View;
use App\Http\Requests\Vendas\AdicionarProdutoRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class VendaPdvController extends Controller
{
    public function create()
    {
        $vendasAbertas = Venda::query()
            ->where('status', Venda::STATUS_ABERTA)
            ->orderByDesc('id')
            ->get();

        $produtos = Produto::query()
            ->where('status', true)
            ->orderBy('nome')
            ->get();

        return view('vendas.pdv', compact('vendasAbertas', 'produtos'));
    }

    public function edit(string $id)
    {
        $venda = Venda::with(['itens.produto', 'cliente.pessoa'])
            ->findOrFail($id);

        if ($venda->status !== Venda::STATUS_ABERTA) {
            abort(404);
        }

        $vendasAbertas = Venda::query()
            ->where('status', Venda::STATUS_ABERTA)
            ->orderByDesc('id')
            ->get();

        $produtos = Produto::query()
            ->where('status', true)
            ->orderBy('nome')
            ->get();

        return view('vendas.pdv', compact(
            'venda',
            'vendasAbertas',
            'produtos'
        ));
    }


    /**
     * Add a product to a sale, creating the sale if necessary.
     */
    public function adicionarProduto(AdicionarProdutoRequest $request): RedirectResponse
    {
        $dados = $request->validated();

        $venda = DB::transaction(function () use ($dados) {
            $produto = Produto::query()
                ->where('status', true)
                ->findOrFail($dados['produto_id']);

            $precoProduto = $dados['preco_produto'];

            if (!empty($dados['venda_id'])) {
                $venda = Venda::query()
                    ->whereKey($dados['venda_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($venda->status !== Venda::STATUS_ABERTA) {
                    abort(422, 'Somente vendas abertas podem ser alteradas.');
                }
            } else {
                $venda = Venda::create([
                    'cliente_id' => null,
                    'funcionario_id' => null,
                    'status' => Venda::STATUS_ABERTA,
                    'valor_produtos' => 0,
                    'descontos' => 0,
                    'outros_custos' => 0,
                    'valor_total' => 0,
                    'data' => now(),
                ]);
            }

            // Procura um item com o mesmo produto e preço.
            $item = $venda->itens()
                ->where('produto_id', $produto->id)
                ->where('preco_unitario', $precoProduto)
                ->first();

            if ($item) {
                $item->quantidade += $dados['quantidade'];
                $item->save();
            } else {
                $venda->itens()->create([
                    'produto_id' => $produto->id,
                    'quantidade' => $dados['quantidade'],
                    'preco_unitario' => $precoProduto,
                    'preco_custo' => $produto->preco_custo,
                ]);
            }

            // Recalcula o valor dos produtos.
            $valorProdutos = $venda->itens()
                ->get()
                ->sum(function ($item) {
                    return round(
                        $item->quantidade * $item->preco_unitario,
                        2
                    );
                });

            $valorProdutos = round($valorProdutos, 2);

            $venda->update([
                'valor_produtos' => $valorProdutos,
                'valor_total' => round(
                    $valorProdutos
                    - $venda->descontos
                    + $venda->outros_custos,
                    2
                ),
            ]);

            return $venda;
        });

        return redirect()
            ->route('vendas.edit', $venda->id)
            ->with('success', 'Produto adicionado à venda.');
    }

    public function destroy(Venda $venda, ItemVenda $item)
    {
        DB::transaction(function () use ($venda, $item) {
            $venda = Venda::query()
                ->whereKey($venda->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($venda->status !== Venda::STATUS_ABERTA) {
                abort(422, 'Somente vendas abertas podem ser alteradas.');
            }

            $item = ItemVenda::query()
                ->whereKey($item->id)
                ->where('venda_id', $venda->id)
                ->lockForUpdate()
                ->firstOrFail();

            $item->delete();

            $valorProdutos = $venda->itens()
                ->get()
                ->sum(function ($item) {
                    return $item->quantidade * $item->preco_unitario;
                });

            $venda->update([
               'valor_produtos' => $valorProdutos,
               'valor_total' => $valorProdutos - $venda->descontos + $venda->outros_custos,
            ]);
        });

        return redirect()->route('vendas.edit', $venda->id)->with('success', 'Produto removida com sucesso.');
    }
}
