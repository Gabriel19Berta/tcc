<?php

namespace App\Http\Controllers\Vendas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendas\AdicionarProdutoRequest;
use App\Http\Requests\Vendas\AdicionarRecebimentoRequest;
use App\Http\Requests\Vendas\AtualizarClienteVendaRequest;
use App\Http\Requests\Vendas\AtualizarFuncionarioVendaRequest;
use App\Http\Requests\Vendas\AtualizarItemVendaRequest;
use App\Http\Requests\Vendas\AtualizarValoresVendaRequest;
use App\Models\Cliente;
use App\Models\FormaPagamento;
use App\Models\Funcionario;
use App\Models\ItemVenda;
use App\Models\Produto;
use App\Models\Recebimento;
use App\Models\Venda;
use App\Support\Formatador;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VendaPdvController extends Controller
{
    public function create(Request $request)
    {
        // Nova venda: responsável o funcionário vinculado ao usuário logado.
        return view('vendas.pdv', $this->dadosPdv() + [
            'funcionarios' => $this->funcionariosDisponiveis(),
            'funcionarioSelecionadoId' => $request->user()->funcionario?->id,
        ]);
    }

    public function edit(Venda $venda)
    {
        if ($venda->status !== Venda::STATUS_ABERTA) {
            return redirect()->route('vendas.index')->with('error', 'Somente vendas abertas podem ser editadas.');
        }

        $venda->load([
            'itens.produto',
            'cliente.pessoa',
            'recebimentos.formaPagamento',
        ]);

        $formasPagamento = FormaPagamento::query()
            ->where('status', true)
            ->orderBy('nome')
            ->get();

        // Venda existente: mantém o responsável salvo, independente de quem a abriu agora.
        return view('vendas.pdv', $this->dadosPdv() + [
            'venda' => $venda,
            'formasPagamento' => $formasPagamento,
            'funcionarios' => $this->funcionariosDisponiveis($venda->funcionario_id),
            'funcionarioSelecionadoId' => $venda->funcionario_id,
        ]);
    }

    public function update(AtualizarItemVendaRequest $request, Venda $venda, ItemVenda $item)
    {
        $dados = $request->validated();

        DB::transaction(function () use ($venda, $item, $dados) {
            $venda = $this->bloquearVendaAberta($venda->id);

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
     * Adiciona um produto à venda, criando a venda quando ainda não existir.
     */
    public function adicionarProduto(AdicionarProdutoRequest $request)
    {
        $dados = $request->validated();

        $funcionarioId = array_key_exists('funcionario_id', $dados)
            ? $dados['funcionario_id']
            : $request->user()->funcionario?->id;

        $venda = DB::transaction(function () use ($dados, $funcionarioId) {
            $precoProduto = $dados['preco_produto'];

            if (!empty($dados['venda_id'])) {
                $venda = $this->bloquearVendaAberta((int) $dados['venda_id']);
            } else {
                $venda = Venda::create([
                    'cliente_id' => null,
                    'funcionario_id' => $funcionarioId,
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
                (float) $dados['quantidade'],
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
            $venda = $this->bloquearVendaAberta($venda->id);

            $item = ItemVenda::query()
                ->whereKey($item->id)
                ->where('venda_id', $venda->id)
                ->lockForUpdate()
                ->firstOrFail();

            $item->delete();

            $this->recalcularTotais($venda);
        });

        return redirect()->route('vendas.edit', $venda->id)->with('success', 'Produto removido com sucesso.');
    }

    public function atualizarFuncionario(AtualizarFuncionarioVendaRequest $request, Venda $venda)
    {
        $dados = $request->validated();

        DB::transaction(function () use ($venda, $dados) {
            $venda = $this->bloquearVendaAberta(
                $venda->id,
                'funcionario_id',
                'Somente vendas abertas podem ter o responsável alterado.'
            );

            $venda->update([
                'funcionario_id' => $dados['funcionario_id'] ?? null,
            ]);
        });

        return redirect()
            ->route('vendas.edit', $venda->id)
            ->with('success', 'Funcionário responsável atualizado com sucesso.');
    }

    public function atualizarCliente(AtualizarClienteVendaRequest $request, Venda $venda)
    {
        $dados = $request->validated();

        DB::transaction(function () use ($venda, $dados) {
            $venda = $this->bloquearVendaAberta($venda->id);

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

    public function atualizarValores(AtualizarValoresVendaRequest $request, Venda $venda)
    {
        $dados = $request->validated();

        $venda = DB::transaction(function () use ($venda, $dados) {
            $venda = $this->bloquearVendaAberta($venda->id);

            $valorProdutos = $this->calcularValorProdutos($venda);

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
                'valor_produtos' => Formatador::moeda($venda->valor_produtos),
                'descontos' => Formatador::moeda($venda->descontos),
                'outros_custos' => Formatador::moeda($venda->outros_custos),
                'valor_total' => Formatador::moeda($venda->valor_total),
                'valor_restante' => Formatador::moeda($venda->valorRestante()),
            ]);
        }

        return redirect()
            ->route('vendas.edit', $venda->id)
            ->with('success', 'Valores da venda atualizados com sucesso.')
            ->with('aba_ativa', 'pagamento');
    }

    public function store(AdicionarRecebimentoRequest $request, Venda $venda)
    {
        // Forma de pagamento ativa já é garantida pela validação do request.
        $dados = $request->validated();

        DB::transaction(function () use ($venda, $dados) {
            $venda = $this->bloquearVendaAberta(
                $venda->id,
                mensagem: 'Somente vendas abertas podem receber pagamentos.'
            );

            // Trabalha com centavos para evitar imprecisão de ponto flutuante.
            $totalEmCentavos = (int) round((float) $venda->valor_total * 100);
            $restanteEmCentavos = $totalEmCentavos - $venda->recebimentosComprometidosEmCentavos();
            $novoValorEmCentavos = (int) round((float) $dados['valor'] * 100);

            if ($novoValorEmCentavos > $restanteEmCentavos) {
                throw ValidationException::withMessages([
                    'valor' => 'O valor informado ultrapassa o valor restante da venda.',
                ]);
            }

            $pago = $dados['status'] === Recebimento::STATUS_PAGO;

            $recebimento = $venda->recebimentos()->create([
                'forma_pagamento_id' => $dados['forma_pagamento_id'],
                'valor' => $dados['valor'],
                'descricao' => $dados['descricao'] ?? '',
                'data_vencimento' => $dados['data_vencimento'],
                'data_pagamento' => $pago ? Carbon::now() : null,
                'status' => $dados['status'],
            ]);

            // Recebimento pago: registra a movimentação integral no histórico de pagamentos.
            if ($pago) {
                $recebimento->registrarPagamentoIntegral();
            }
        });

        return redirect()
            ->route('vendas.edit', $venda->id)
            ->with('success', 'Recebimento adicionado com sucesso.')
            ->with('aba_ativa', 'pagamento');
    }

    public function destroyRecebimento(Venda $venda, Recebimento $recebimento)
    {
        DB::transaction(function () use ($venda, $recebimento) {
            $venda = $this->bloquearVendaAberta(
                $venda->id,
                'recebimento',
                'Não é possível remover recebimentos de uma venda que não está aberta.'
            );

            $recebimento = $venda->recebimentos()
                ->whereKey($recebimento->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Pagamentos registrados (parciais) são movimentações financeiras: não podem ser apagados.
            // Recebimento pago no PDV pode ser removido junto com sua movimentação integral.
            // Estornos também são histórico financeiro e impedem a remoção.
            if (
                $recebimento->estornos()->exists()
                || ($recebimento->status !== Recebimento::STATUS_PAGO && $recebimento->pagamentos()->exists())
            ) {
                throw ValidationException::withMessages([
                    'recebimento' => 'Não é possível remover este recebimento porque ele possui pagamentos registrados.',
                ]);
            }

            $recebimento->pagamentos()->delete();
            $recebimento->delete();
        });

        return redirect()
            ->route('vendas.edit', $venda)
            ->with('aba_ativa', 'pagamento')
            ->with('success', 'Parcela removida com sucesso.');
    }

    public function finalizar(Venda $venda)
    {
        DB::transaction(function () use ($venda) {

            // Bloqueia a venda durante a finalização (somente vendas abertas).
            $venda = $this->bloquearVendaAberta($venda->id, mensagem: 'Esta venda não está aberta.');

            // Busca os itens da venda.
            $itens = $venda->itens()
                ->lockForUpdate()
                ->get();

            // Não permite finalizar uma venda sem produtos.
            if ($itens->isEmpty()) {
                throw ValidationException::withMessages([
                    'venda' => 'Adicione pelo menos um produto antes de finalizar a venda.',
                ]);
            }

            // Recalcula os valores antes da finalização.
            $this->recalcularTotais($venda);

            $venda->refresh();

            // Garante que o desconto não ultrapasse o valor dos produtos.
            if ((float) $venda->descontos > (float) $venda->valor_produtos) {
                throw ValidationException::withMessages([
                    'desconto' => 'O desconto não pode ser maior que o valor dos produtos.',
                ]);
            }

            // Não permite valores negativos.
            if ((float) $venda->valor_total < 0) {
                throw ValidationException::withMessages([
                    'venda' => 'O valor total da venda não pode ser negativo.',
                ]);
            }

            /*
             * Agrupa os produtos para garantir que, caso o mesmo produto
             * apareça em mais de um item, seu estoque seja validado e
             * atualizado considerando a quantidade total.
             */
            $quantidadesPorProduto = $itens->groupBy('produto_id');

            foreach ($quantidadesPorProduto as $produtoId => $itensProduto) {

                $produto = Produto::query()
                    ->whereKey($produtoId)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Produtos sem controle de estoque não recebem baixa.
                if (!$produto->controla_estoque) {
                    continue;
                }

                $quantidadeVendida = $itensProduto->sum('quantidade');

                // Estoque disponível em centésimos.
                $estoqueEmCentésimos = (int) round(
                    (float) $produto->quantidade * 100
                );

                // Quantidade vendida em centésimos.
                $vendidoEmCentésimos = (int) round(
                    (float) $quantidadeVendida * 100
                );

                if ($vendidoEmCentésimos > $estoqueEmCentésimos) {
                    throw ValidationException::withMessages([
                        'venda' => "Estoque insuficiente para o produto {$produto->nome}.",
                    ]);
                }

                // Realiza a baixa do estoque.
                $novoEstoque = (
                        $estoqueEmCentésimos - $vendidoEmCentésimos
                    ) / 100;

                $produto->update([
                    'quantidade' => $novoEstoque,
                ]);
            }

            $totalEmCentésimos = (int) round(
                (float) $venda->valor_total * 100
            );

            // Impede finalizar uma venda com recebimentos (pagos e pendentes) acima do total.
            if ($venda->recebimentosComprometidosEmCentavos() > $totalEmCentésimos) {
                throw ValidationException::withMessages([
                    'recebimento' => 'O total dos recebimentos ultrapassa o valor da venda. Ajuste os recebimentos antes de finalizar.',
                ]);
            }

            // Define se a venda está quitada ou apenas finalizada (regra do Model Venda).
            $venda->update([
                'status' => $venda->situacaoPorPagamentos(),
            ]);
        });

        return redirect()
            ->route('vendas.index')
            ->with('success', 'Venda finalizada com sucesso.');
    }

    /**
     * Dados comuns às telas de nova venda e de venda em andamento.
     */
    private function dadosPdv(): array
    {
        return [
            'vendasAbertas' => Venda::query()
                ->where('status', Venda::STATUS_ABERTA)
                ->orderByDesc('id')
                ->get(),
            'produtos' => Produto::query()
                ->where('status', true)
                ->orderBy('nome')
                ->get(),
            'clientes' => $this->clientesAtivos(),
        ];
    }

    /**
     * Bloqueia a venda (lockForUpdate, dentro da transação) e garante que ela
     * continua aberta. Toda alteração feita pelo PDV passa por aqui, com a
     * mensagem exibida no campo indicado da tela.
     */
    private function bloquearVendaAberta(
        int $vendaId,
        string $chave = 'venda',
        string $mensagem = 'Somente vendas abertas podem ser alteradas.'
    ): Venda {
        $venda = Venda::query()
            ->whereKey($vendaId)
            ->lockForUpdate()
            ->firstOrFail();

        if ($venda->status !== Venda::STATUS_ABERTA) {
            throw ValidationException::withMessages([$chave => $mensagem]);
        }

        return $venda;
    }

    /**
     * Soma dos itens (quantidade × preço unitário), arredondada em 2 casas.
     */
    private function calcularValorProdutos(Venda $venda): float
    {
        $valorProdutos = (float) $venda->itens()
            ->get()
            ->sum(fn (ItemVenda $item) => $item->quantidade * $item->preco_unitario);

        return round($valorProdutos, 2);
    }

    private function recalcularTotais(Venda $venda): void
    {
        $valorProdutos = $this->calcularValorProdutos($venda);

        $valorTotal = $valorProdutos
            - $venda->descontos
            + $venda->outros_custos;

        $venda->update([
            'valor_produtos' => $valorProdutos,
            'valor_total' => round($valorTotal, 2),
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

    /**
     * Funcionários ativos para o select de responsável.
     */
    private function funcionariosDisponiveis(?int $funcionarioAtualId = null)
    {
        return Funcionario::query()
            ->join('pessoas', 'pessoas.id', '=', 'funcionarios.pessoa_id')
            ->where(function ($query) use ($funcionarioAtualId) {
                $query->where('pessoas.status', true)
                    ->orWhere('funcionarios.id', $funcionarioAtualId);
            })
            ->select('funcionarios.*')
            ->with('pessoa')
            ->orderBy('pessoas.nome')
            ->get();
    }

    private function validarEstoque(Produto $produto, Venda $venda, float $quantidadeAdicional, ?int $itemIgnoradoId = null): void
    {
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

        $chaveErro = $itemIgnoradoId ? 'quantidade_item_' . $itemIgnoradoId : 'produto_id';

        if ($solicitadoEmCentésimos > $disponivelEmCentésimos) {
            throw ValidationException::withMessages([
                $chaveErro => sprintf(
                    'Estoque insuficiente. Disponível: %s.',
                    number_format($estoqueDisponivel, 2, ',', '.')
                ),
            ]);
        }
    }
}
