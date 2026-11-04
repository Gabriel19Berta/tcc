<?php

namespace App\Http\Controllers\Vendas;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\Venda;
use App\Http\Controllers\Vendas\View;
use App\Http\Requests\Vendas\AdicionarProdutoRequest;
use Illuminate\Support\Facades\DB;

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
    public function adicionarProduto(AdicionarProdutoRequest $request)
    {
        $dados = $request->validated();

        $venda = DB::transaction(function () use ($dados) {
            $produto = Produto::query()
                ->where('status', true)
                ->findOrFail($dados['produto_id']);

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

            $item = $venda->itens()
                ->where('produto_id', $produto->id)
                ->where('preco_unitario', $dados['preco_produto'])
                ->first();

            if ($item) {
                $item->quantidade += $dados['quantidade'];
                $item->save();
            } else {
                $venda->itens()->create([
                    'produto_id' => $produto->id,
                    'quantidade' => $dados['quantidade'],
                    'preco_unitario' => $produto->preco_venda,
                    'preco_custo' => $produto->preco_custo,
                ]);
            }

            $valorProdutos = $venda->itens()
                ->get()
                ->sum(fn ($item) => $item->quantidade * $item->preco_unitario);

            $venda->update([
                'valor_produtos' => $valorProdutos,
                'valor_total' => $valorProdutos
                    - $venda->descontos
                    + $venda->outros_custos,
            ]);

            return $venda;
        });

        return redirect()
            ->route('vendas.edit', $venda->id)
            ->with('success', 'Produto adicionado à venda.');
    }
}
