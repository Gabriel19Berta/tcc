<?php

namespace App\Http\Controllers\Vendas;

use App\Http\Controllers\Controller;
use App\Models\ItemVenda;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\Cliente;
use App\Models\FormaPagamento;
use App\Models\Recebimento;
use Illuminate\View\View;
use App\Http\Requests\Vendas\AdicionarProdutoRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Vendas\AtualizarItemVendaRequest;
use App\Http\Requests\Vendas\AtualizarClienteVendaRequest;
use App\Http\Requests\Vendas\AtualizarValoresVendaRequest;
use App\Http\Requests\Vendas\AdicionarRecebimentoRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

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
        $venda = Venda::with([
            'itens.produto',
            'cliente.pessoa',
            'recebimentos.formaPagamento',
        ])->findOrFail($id);

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

        $formasPagamento = FormaPagamento::query()
            ->where('status', true)
            ->orderBy('nome')
            ->get();

        $totalRecebido = $venda->recebimentos()
            ->whereIn('status', ['pago', 'pendente'])
            ->sum('valor');

        $saldoRestante = $venda->valor_total - $totalRecebido;

        return view('vendas.pdv', compact(
            'venda',
            'vendasAbertas',
            'produtos',
            'clientes',
            'formasPagamento',
            'totalRecebido',
            'saldoRestante'
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

            $produto = Produto::query()
                ->lockForUpdate()
                ->findOrFail($item->produto_id);

            $this->validarEstoque($produto, $venda, (float) $dados['quantidade'], $item->id);

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
    public function adicionarProduto(AdicionarProdutoRequest $request)
    {
        $dados = $request->validated();

        $venda = DB::transaction(function () use ($dados) {
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

            $produto = Produto::query()
                ->where('status', true)
                ->lockForUpdate()
                ->findOrFail($dados['produto_id']);

            // Verifica se existe estoque suficiente
            $this->validarEstoque(
                $produto,
                $venda,
                (float)$dados['quantidade'],
            );

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
                    ->findOrFail($dados['cliente_id']);
            }

            $venda->update([
                'cliente_id' => $dados['cliente_id'] ?? null,
            ]);
        });

        return redirect()
            ->route('vendas.edit', $venda->id)
            ->with('success', 'Cliente atualizado com sucesso.')
            ->with('aba_ativa', 'cliente');
    }

    private function validarEstoque(Produto $produto, Venda $venda, float $quantidadeAdicional, ?int $itemIgnoradoId = null) {
        // Produtos que não controlam estoque não precisam dessa validação.
        if (!$produto->controla_estoque) {
            return;
        }

        // Soma as quantidades do mesmo produto já presentes na venda.
        $consulta = $venda->itens()
            ->where('produto_id', $produto->id);

        // Na atualização, desconsidera o item que está sendo alterado.
        if ($itemIgnoradoId !== null) {
            $consulta->where('id', '!=', $itemIgnoradoId);
        }

        $quantidadeJaAdicionada = (float) $consulta->sum('quantidade');
        $quantidadeSolicitada = $quantidadeJaAdicionada + $quantidadeAdicional;
        $estoqueDisponivel = (float) ($produto->quantidade ?? 0);

        // Compara em centésimos para evitar pequenas imprecisões de float.
        $solicitadoEmCentésimos = (int) round($quantidadeSolicitada * 100);
        $disponivelEmCentésimos = (int) round($estoqueDisponivel * 100);

        $chaveErro = $itemIgnoradoId  ? 'quantidade_item_' . $itemIgnoradoId : 'produto_id';

        if ($solicitadoEmCentésimos > $disponivelEmCentésimos) {
            throw ValidationException::withMessages([
                $chaveErro => sprintf(
                    'Estoque insuficiente. Disponível: %s.',
                    number_format($estoqueDisponivel, 2, ',', '.')
                ),
            ]);
        }
    }

    public function atualizarValores(AtualizarValoresVendaRequest $request, Venda $venda)
    {
        $dados = $request->validated();

        $venda = DB::transaction(function () use ($venda, $dados) {
            $venda = Venda::query()
                ->whereKey($venda->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($venda->status !== Venda::STATUS_ABERTA) {
                throw ValidationException::withMessages([
                    'status' => 'Somente vendas abertas podem receber pagamentos.',
                ]);
            }

            $valorProdutos = (float) $venda->itens()
                ->get()
                ->sum(fn ($item) => $item->quantidade * $item->preco_unitario);

            $valorProdutos = round($valorProdutos, 2);

            if ($dados['tipo_desconto'] === 'percentual') {
                $desconto = round(
                    $valorProdutos * ((float) $dados['desconto'] / 100),
                    2
                );
            } else {
                $desconto = round((float) $dados['desconto'], 2);
            }

            if ($desconto > $valorProdutos) {
                throw ValidationException::withMessages([
                    'desconto' => 'O desconto não pode ser maior que o valor dos produtos.',
                ]);
            }

            $venda->update([
                'descontos' => $desconto,
                'outros_custos' => round((float) $dados['outros_custos'], 2),
            ]);

            $this->recalcularTotais($venda);

            return $venda->fresh();
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'valor_produtos' => number_format($venda->valor_produtos, 2, ',', '.'),
                'descontos' => number_format($venda->descontos, 2, ',', '.'),
                'outros_custos' => number_format($venda->outros_custos, 2, ',', '.'),
                'valor_total' => number_format($venda->valor_total, 2, ',', '.'),
            ]);
        }

        return redirect()
            ->route('vendas.edit', $venda->id)
            ->with('success', 'Valores da venda atualizados com sucesso.')
            ->with('aba_ativa', 'pagamento');
    }

    public function store(AdicionarRecebimentoRequest $request, Venda $venda)
    {
        $dados = $request->validated();

        DB::transaction(function () use ($venda, $dados) {
            $venda = Venda::query()
                ->whereKey($venda->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($venda->status !== Venda::STATUS_ABERTA) {
                abort(422, 'Somente vendas abertas podem receber pagamentos.');
            }

            $formaPagamentoAtiva = FormaPagamento::query()
                ->whereKey($dados['forma_pagamento_id'])
                ->where('status', true)
                ->exists();

            if (!$formaPagamentoAtiva) {
                throw ValidationException::withMessages([
                    'forma_pagamento_id' => 'A forma de pagamento selecionada não está disponível.',
                ]);
            }

            // Soma os recebimentos que ainda não foram cancelados.
            $valorRecebido = $venda->recebimentos()
                ->whereIn('status', ['pago', 'pendente'])
                ->sum('valor');

            // Trabalha com centavos para evitar imprecisão de ponto flutuante.
            $totalEmCentavos = (int) round((float) $venda->valor_total * 100);
            $recebidoEmCentavos = (int) round((float) $valorRecebido * 100);
            $novoValorEmCentavos = (int) round((float) $dados['valor'] * 100);

            $restanteEmCentavos = $totalEmCentavos - $recebidoEmCentavos;

            if ($novoValorEmCentavos > $restanteEmCentavos) {
                throw ValidationException::withMessages([
                    'valor' => 'O valor informado ultrapassa o valor restante da venda.',
                ]);
            }

            $pago = $dados['status'] === 'pago';

            $venda->recebimentos()->create([
                'forma_pagamento_id' => $dados['forma_pagamento_id'],
                'valor' => $dados['valor'],
                'descricao' => $dados['descricao'] ?? '',
                'data_vencimento' => $dados['data_vencimento'],
                'data_pagamento' => $pago ? Carbon::now() : null,
                'status' => $dados['status'],
            ]);
        });

        return redirect()
            ->route('vendas.edit', $venda->id)
            ->with('success', 'Recebimento adicionado com sucesso.')
            ->with('aba_ativa', 'pagamento');
    }

    public function destroyRecebimento(Venda $venda, Recebimento $recebimento)
    {
        DB::transaction(function () use ($venda, $recebimento) {
            $venda = Venda::query()
                ->lockForUpdate()
                ->findOrFail($venda->id);

            if ($venda->status !== Venda::STATUS_ABERTA) {
                throw ValidationException::withMessages([
                    'recebimento' => 'Não é possível remover recebimentos de uma venda que não está aberta.',
                ]);
            }

            $recebimento = $venda->recebimentos()
                ->whereKey($recebimento->id)
                ->lockForUpdate()
                ->firstOrFail();

            $recebimento->delete();
        });

        return redirect()
            ->route('vendas.edit', $venda)
            ->with('aba_ativa', 'pagamento')
            ->with('success', 'Parcela removida com sucesso.');
    }
}
