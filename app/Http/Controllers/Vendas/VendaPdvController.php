<?php

namespace App\Http\Controllers\Vendas;

use App\Http\Controllers\Controller;
use App\Models\ItemVenda;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\Cliente;
use Illuminate\View\View;
use App\Http\Requests\Vendas\AdicionarProdutoRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Vendas\AtualizarItemVendaRequest;
use App\Http\Requests\Vendas\AtualizarClienteVendaRequest;

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

        $clientes = $this->clientesAtivos();

        return view('vendas.pdv', compact('vendasAbertas', 'produtos', 'clientes'));
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
            
        $clientes = $this->clientesAtivos();

        return view('vendas.pdv', compact(
            'venda',
            'vendasAbertas',
            'produtos',
            'clientes'
        ));
    }

    public function update(AtualizarItemVendaRequest $request, Venda $venda, ItemVenda $item)
    {
        $dados = $request->validated();

        DB::transaction(function () use ($venda, $item, $dados) {
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

            $item->update([
                'quantidade' => $dados['quantidade'],
            ]);

            $this->recalcularTotais($venda);
        });

        return redirect()
            ->route('vendas.edit', $venda->id)
            ->with('success', 'Quantidade atualizada com sucesso.');
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

            $this->recalcularTotais($venda);

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

            $this->recalcularTotais($venda);
        });

        return redirect()->route('vendas.edit', $venda->id)->with('success', 'Produto removida com sucesso.');
    }

    private function recalcularTotais(Venda $venda)
    {
        $valorProdutos = $venda->itens()
            ->get()
            ->sum(function ($item) {
                return $item->quantidade * $item->preco_unitario;
            });

        $valorProdutos = round($valorProdutos, 2);

        $valorTotal = $valorProdutos
            - $venda->descontos
            + $venda->outros_custos;
            
            $venda->update([
                'valor_produtos' => $valorProdutos,
                'valor_total' => round($valorTotal, 2)
            ]);
    }

    private function clientesAtivos()
    {
        return Cliente::query()
            ->join('pessoas', 'pessoas.id', '=', 'clientes.pessoa_id')
            ->where('pessoas.status', true)
            ->select('clientes.*')
            ->with('pessoa')
            ->orderBy('pessoas.nome')
            ->get();
    }

    public function atualizarCliente(AtualizarClienteVendaRequest $request, Venda $venda)
    {
        $dados = $request->validated();

        DB::transaction(function () use ($venda, $dados) {
            $venda = Venda::query()
                ->whereKey($venda->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($venda->status !== Venda::STATUS_ABERTA) {
                abort(422, 'Somente vendas abertas podem ser alteradas.');
            }

            if (!empty($dados['cliente_id'])) {
                Cliente::query()
                    ->whereHas('pessoa', function ($query) {
                        $query->where('status', true);
                    })
                    ->findtOrFail($dados['cliente_id']);
            }

            $venda->update([
                'cliente_id' => $dados['cliente_id'] ?? null,
            ]);
        });

        return redirect()
            ->route('vendas.edit', $venda->id)
            ->with('success', 'Cliente atualizado com sucesso.');
    }
}
