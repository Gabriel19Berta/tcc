<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vendas\AtualizarRecebimentoRequest;
use App\Http\Requests\Vendas\LancarRecebimentoRequest;
use App\Http\Requests\Vendas\ReceberRecebimentoRequest;
use App\Models\FormaPagamento;
use App\Models\Pagamento;
use App\Models\Recebimento;
use App\Models\Venda;
use App\Support\Formatador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecebimentoController extends Controller
{
    private const MSG_SEM_SALDO = 'Não é possível lançar novos recebimentos. '
        . 'O valor total da venda já está comprometido com os recebimentos existentes.';

    private const MSG_NAO_RECEBIVEL = 'Somente recebimentos pendentes podem ser recebidos.';

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
            // Valor recebido ainda não estornado (informado na confirmação do estorno), sem consulta por linha.
            ->withSum(['pagamentos as valor_recebido' => fn ($query) => $query->whereNull('estorno_id')], 'valor')
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

    /**
     * Consulta de um recebimento (somente leitura).
     */
    public function show(Recebimento $recebimento)
    {
        $recebimento->load(['venda.cliente.pessoa', 'formaPagamento', 'pagamentos.formaPagamento', 'pagamentos.estorno', 'estornos.user']);

        return view('recebimentos.show', compact('recebimento'));
    }

    /**
     * Estorna o recebimento como um todo: registra o estorno da soma dos
     * pagamentos ainda válidos e reabre o recebimento como pendente.
     */
    public function estornar(Recebimento $recebimento)
    {
        $resultado = DB::transaction(function () use ($recebimento) {
            // Mesma ordem de bloqueio das demais operações: venda e depois recebimento.
            $venda = Venda::query()->lockForUpdate()->findOrFail($recebimento->venda_id);
            $recebimento = Recebimento::query()->lockForUpdate()->findOrFail($recebimento->id);

            if (!in_array($recebimento->status, [Recebimento::STATUS_PENDENTE, Recebimento::STATUS_PAGO], true)) {
                return 'Somente recebimentos pendentes ou pagos podem ser estornados.';
            }

            // Valor do estorno = pagamentos ainda não estornados (recalculado com os registros bloqueados).
            // Depois de estornados, eles deixam de contar: um segundo estorno não encontra o que estornar.
            $pagamentos = $recebimento->pagamentos()->whereNull('estorno_id')->lockForUpdate()->get();
            $estornoCentavos = $pagamentos->sum(fn ($pagamento) => (int) round((float) $pagamento->valor * 100));

            if ($estornoCentavos <= 0) {
                return 'Este recebimento não possui valores recebidos para estornar.';
            }

            // Registro próprio do estorno, ligado aos pagamentos que ele desfaz
            // (valores, datas e formas dos pagamentos permanecem inalterados).
            $estorno = $recebimento->estornos()->create([
                'user_id' => auth()->id(),
                'valor' => $estornoCentavos / 100,
                'data_estorno' => now(),
            ]);

            Pagamento::query()->whereKey($pagamentos->modelKeys())->update(['estorno_id' => $estorno->id]);

            // Reabre o recebimento: volta a pendente, com saldo igual ao valor original,
            // e pode receber novos pagamentos normalmente.
            $recebimento->update([
                'status' => Recebimento::STATUS_PENDENTE,
                'data_pagamento' => null,
            ]);

            // Mesma regra da finalização: sem o valor estornado, uma venda quitada volta a finalizada.
            $venda->atualizarSituacaoFinanceira();

            return ['success', "Recebimento #{$recebimento->id} estornado. Valor estornado: "
                . Formatador::reais($estornoCentavos / 100) . '. O recebimento voltou a ficar pendente.'];
        });

        if (is_string($resultado)) {
            return $this->voltarParaListagem('recebimentos.index', $resultado);
        }

        [$tipo, $mensagem] = $resultado;

        // Volta para a listagem (com os filtros, quando o estorno foi feito por ela).
        return $this->voltarParaListagem('recebimentos.index', $mensagem, $tipo);
    }

    /**
     * Cancela (encerra) um recebimento pendente sem valor recebido. Com valores
     * recebidos, é preciso estornar antes. Pagamentos e estornos são preservados.
     */
    public function cancelar(Recebimento $recebimento)
    {
        $resultado = DB::transaction(function () use ($recebimento) {
            // Mesma ordem de bloqueio das demais operações: venda e depois recebimento.
            $venda = Venda::query()->lockForUpdate()->findOrFail($recebimento->venda_id);
            $recebimento = Recebimento::query()->lockForUpdate()->findOrFail($recebimento->id);

            if ($recebimento->status === Recebimento::STATUS_CANCELADO) {
                return 'Este recebimento já está cancelado.';
            }

            // Pago (ou pendente com valor recebido): precisa estornar antes de cancelar.
            $recebidoCentavos = $recebimento->pagamentos()->whereNull('estorno_id')->lockForUpdate()->get()
                ->sum(fn ($pagamento) => (int) round((float) $pagamento->valor * 100));

            if ($recebimento->status !== Recebimento::STATUS_PENDENTE || $recebidoCentavos > 0) {
                return 'Não é possível cancelar este recebimento porque ele possui valores recebidos ('
                    . Formatador::reais($recebidoCentavos / 100) . '). Realize o estorno antes de cancelar.';
            }

            $recebimento->update(['status' => Recebimento::STATUS_CANCELADO]);

            // Mesma regra das demais operações para a situação da venda.
            $venda->atualizarSituacaoFinanceira();

            return ['success', "Recebimento #{$recebimento->id} cancelado."];
        });

        if (is_string($resultado)) {
            return $this->voltarParaListagem('recebimentos.index', $resultado);
        }

        [$tipo, $mensagem] = $resultado;

        return $this->voltarParaListagem('recebimentos.index', $mensagem, $tipo);
    }

    /**
     * Formulário de edição (somente recebimentos pendentes).
     */
    public function edit(Recebimento $recebimento)
    {
        if ($recebimento->status !== Recebimento::STATUS_PENDENTE) {
            return $this->voltarParaListagem('recebimentos.index', 'Somente recebimentos pendentes podem ser editados.');
        }

        $recebimento->load(['venda.cliente.pessoa', 'formaPagamento']);

        $formasPagamento = $this->formasPagamentoDisponiveis($recebimento);

        // Valor máximo que este recebimento pode ter sem ultrapassar o total da venda.
        $limiteCentavos = (int) round((float) $recebimento->venda->valor_total * 100)
            - $recebimento->venda->recebimentosComprometidosEmCentavos(ignorarId: $recebimento->id);
        $valorMaximo = max(0, $limiteCentavos) / 100;

        return view('recebimentos.edit', compact('recebimento', 'formasPagamento', 'valorMaximo'));
    }

    /**
     * Atualiza forma de pagamento, valor e vencimento de um recebimento pendente.
     *
     * A soma dos recebimentos não cancelados da venda (pagos + pendentes, com o
     * novo valor deste) pode ser igual ao total da venda, mas nunca superior.
     */
    public function update(AtualizarRecebimentoRequest $request, Recebimento $recebimento)
    {
        $dados = $request->validated();

        $erro = DB::transaction(function () use ($recebimento, $dados) {
            // Bloqueia a venda primeiro (mesma ordem do PDV), serializando as
            // operações financeiras dela; depois o próprio recebimento.
            $venda = Venda::query()->lockForUpdate()->findOrFail($recebimento->venda_id);
            $recebimento = Recebimento::query()->lockForUpdate()->findOrFail($recebimento->id);

            if ($recebimento->status !== Recebimento::STATUS_PENDENTE) {
                return 'Somente recebimentos pendentes podem ser editados.';
            }

            // Compara em centavos, com os valores atuais do banco.
            $totalVendaCentavos = (int) round((float) $venda->valor_total * 100);
            $outrosCentavos = $venda->recebimentosComprometidosEmCentavos(ignorarId: $recebimento->id, bloquear: true);
            $novoValorCentavos = (int) round((float) $dados['valor'] * 100);

            if ($outrosCentavos + $novoValorCentavos > $totalVendaCentavos) {
                throw ValidationException::withMessages([
                    'valor' => 'O valor ultrapassa o total da venda. Valor máximo para este recebimento: '
                        . Formatador::reais(max(0, $totalVendaCentavos - $outrosCentavos) / 100) . '.',
                ]);
            }

            // Com pagamentos parciais, o valor precisa continuar acima do já recebido
            $recebidoCentavos = $recebimento->totalRecebidoEmCentavos();

            if ($recebidoCentavos > 0 && $novoValorCentavos <= $recebidoCentavos) {
                throw ValidationException::withMessages([
                    'valor' => 'O valor deve ser maior que o total já recebido ('
                        . Formatador::reais($recebidoCentavos / 100) . ').',
                ]);
            }

            // Somente os campos editáveis: venda, status e data de pagamento permanecem.
            $recebimento->update([
                'forma_pagamento_id' => $dados['forma_pagamento_id'],
                'valor' => $dados['valor'],
                'data_vencimento' => $dados['data_vencimento'],
            ]);

            return null;
        });

        if ($erro) {
            return $this->voltarParaListagem('recebimentos.index', $erro);
        }

        // Mesmo padrão das demais edições: volta para a listagem.
        return redirect()
            ->route('recebimentos.index')
            ->with('success', 'Recebimento atualizado com sucesso.');
    }

    /**
     * Confirmação do pagamento de um recebimento pendente.
     */
    public function receber(Recebimento $recebimento)
    {
        if ($recebimento->status !== Recebimento::STATUS_PENDENTE) {
            return $this->voltarParaListagem('recebimentos.index', self::MSG_NAO_RECEBIVEL);
        }

        $recebimento->load(['venda.cliente.pessoa', 'formaPagamento', 'pagamentos']);

        $formasPagamento = $this->formasPagamentoDisponiveis($recebimento);

        $totalRecebido = $recebimento->totalRecebidoEmCentavos() / 100;
        $saldoRestante = $recebimento->saldoEmCentavos() / 100;

        return view('recebimentos.receber', compact('recebimento', 'formasPagamento', 'totalRecebido', 'saldoRestante'));
    }

    /**
     * Registra um pagamento (parcial ou total) do recebimento: cria uma nova
     * movimentação com o valor informado, a forma utilizada e a data atual.
     * Saldo restante = valor original − pagamentos; o recebimento só passa
     * a "pago" quando o saldo chega a zero.
     */
    public function registrarPagamento(ReceberRecebimentoRequest $request, Recebimento $recebimento)
    {
        $dados = $request->validated();

        // Retorna uma mensagem de erro (string) ou [tipo, mensagem] de sucesso.
        $resultado = DB::transaction(function () use ($recebimento, $dados) {
            // Mesma ordem de bloqueio do PDV, da edição e do lançamento: venda e depois recebimento.
            $venda = Venda::query()->lockForUpdate()->findOrFail($recebimento->venda_id);
            $recebimento = Recebimento::query()->lockForUpdate()->findOrFail($recebimento->id);

            // Revalida com o valor atual do banco (impede pagar recebimento quitado ou cancelado).
            if ($recebimento->status !== Recebimento::STATUS_PENDENTE) {
                return self::MSG_NAO_RECEBIVEL;
            }

            // Saldo recalculado no banco, em centavos: valor original − pagamentos registrados.
            $saldoCentavos = $recebimento->saldoEmCentavos();
            $valorCentavos = (int) round((float) $dados['valor'] * 100);

            if ($valorCentavos > $saldoCentavos) {
                throw ValidationException::withMessages([
                    'valor' => 'O valor ultrapassa o saldo restante do recebimento. Valor máximo: '
                        . Formatador::reais(max(0, $saldoCentavos) / 100) . '.',
                ]);
            }

            // Cada confirmação gera uma nova movimentação (as anteriores são preservadas).
            // Data e horário informados pelo usuário (já validados no request).
            $dataPagamento = Carbon::createFromFormat('Y-m-d H:i', "{$dados['data_pagamento']} {$dados['hora_pagamento']}")
                ->startOfMinute();

            $recebimento->pagamentos()->create([
                'forma_pagamento_id' => $dados['forma_pagamento_id'],
                'valor' => $dados['valor'],
                'data_pagamento' => $dataPagamento,
            ]);

            $restanteCentavos = $saldoCentavos - $valorCentavos;

            // Só fica "pago" quando o saldo chega a zero (valor original preservado).
            if ($restanteCentavos === 0) {
                // Data da quitação: o pagamento mais recente informado (podem ser registrados fora de ordem).
                $recebimento->update([
                    'data_pagamento' => $recebimento->pagamentos()->whereNull('estorno_id')->max('data_pagamento'),
                    'status' => Recebimento::STATUS_PAGO,
                ]);

                // Venda concluída: mesma regra da finalização (pago ≥ total → quitada).
                // Venda aberta permanece aberta (a finalização do PDV define a situação).
                $venda->atualizarSituacaoFinanceira();
            }

            return $restanteCentavos === 0
                ? ['success', "Recebimento #{$recebimento->id} quitado com o pagamento de " . Formatador::reais($valorCentavos / 100) . '.']
                : ['success', 'Pagamento de ' . Formatador::reais($valorCentavos / 100) . " registrado no recebimento #{$recebimento->id}. "
                    . 'Saldo restante: ' . Formatador::reais($restanteCentavos / 100) . '.'];
        });

        if (is_string($resultado)) {
            return $this->voltarParaListagem('recebimentos.index', $resultado);
        }

        [$tipo, $mensagem] = $resultado;

        return redirect()
            ->route('recebimentos.index')
            ->with($tipo, $mensagem);
    }

    /**
     * Formulário de lançamento de recebimento para uma venda finalizada.
     */
    public function create(Venda $venda)
    {
        if (!$venda->permiteLancarRecebimento()) {
            return $this->voltarParaListagem('vendas.index', $this->motivoSemLancamento($venda));
        }

        // Saldo disponível = total da venda − recebimentos não cancelados (mesma regra do store).
        $totalCentavos = (int) round((float) $venda->valor_total * 100);
        $lancadoCentavos = $venda->recebimentosComprometidosEmCentavos();

        // Sem saldo, nem apresenta a tela (mesmo comportamento da venda quitada).
        if ($totalCentavos - $lancadoCentavos <= 0) {
            return $this->voltarParaListagem('vendas.index', self::MSG_SEM_SALDO);
        }

        $venda->load('cliente.pessoa');

        $totalLancado = $lancadoCentavos / 100;
        $saldoDisponivel = ($totalCentavos - $lancadoCentavos) / 100;

        $formasPagamento = FormaPagamento::query()
            ->where('status', true)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('recebimentos.create', compact('venda', 'totalLancado', 'saldoDisponivel', 'formasPagamento'));
    }

    /**
     * Lança um novo recebimento (pendente) para a venda.
     *
     * Saldo disponível = total da venda − recebimentos não cancelados (pagos + pendentes).
     * O novo valor pode ser igual ao saldo, mas nunca superior.
     */
    public function store(LancarRecebimentoRequest $request, Venda $venda)
    {
        $dados = $request->validated();

        $erro = DB::transaction(function () use ($venda, $dados) {
            // Bloqueia a venda (mesma ordem do PDV e da edição) e revalida o status.
            $venda = Venda::query()->lockForUpdate()->findOrFail($venda->id);

            if (!$venda->permiteLancarRecebimento()) {
                return $this->motivoSemLancamento($venda);
            }

            // Compara em centavos, com os valores atuais do banco.
            $totalCentavos = (int) round((float) $venda->valor_total * 100);
            $saldoCentavos = $totalCentavos - $venda->recebimentosComprometidosEmCentavos(bloquear: true);

            if ($saldoCentavos <= 0) {
                return self::MSG_SEM_SALDO;
            }

            if ((int) round((float) $dados['valor'] * 100) > $saldoCentavos) {
                throw ValidationException::withMessages([
                    'valor' => 'O valor ultrapassa o saldo disponível da venda. Valor máximo: '
                        . Formatador::reais($saldoCentavos / 100) . '.',
                ]);
            }

            // Venda definida pela rota. Status escolhido (pago ou pendente, validado
            // no request); recebimento pago registra a data de pagamento, como no PDV.
            $pago = $dados['status'] === Recebimento::STATUS_PAGO;

            $novo = $venda->recebimentos()->create([
                'forma_pagamento_id' => $dados['forma_pagamento_id'],
                'valor' => $dados['valor'],
                'descricao' => '',
                'data_vencimento' => $dados['data_vencimento'],
                'data_pagamento' => $pago ? now() : null,
                'status' => $dados['status'],
            ]);

            // Lançado como pago: registra a movimentação integral no histórico.
            if ($pago) {
                $novo->registrarPagamentoIntegral();
            }

            // Mesma regra da finalização: se o total pago cobrir a venda, ela fica quitada.
            $venda->atualizarSituacaoFinanceira();

            return null;
        });

        if ($erro) {
            // Não volta ao formulário (ele também seria recusado): vai para a listagem de vendas.
            return redirect()
                ->route('vendas.index')
                ->with('error', $erro);
        }

        return redirect()
            ->route('recebimentos.index')
            ->with('success', "Recebimento lançado com sucesso para a venda #{$venda->id}.");
    }

    /**
     * Volta para a listagem informada com a mensagem de erro, preservando os
     * filtros quando o usuário veio dela. Nunca volta para a tela que bloqueou
     * a operação (o que poderia causar redirecionamento em laço).
     */
    private function voltarParaListagem(string $rota, string $mensagem, string $tipo = 'error'): RedirectResponse
    {
        $anterior = url()->previous();
        $destino = strtok($anterior, '?') === route($rota) ? $anterior : route($rota);

        return redirect()->to($destino)->with($tipo, $mensagem);
    }

    /**
     * Formas de pagamento ativas, mais a atual do recebimento (mesmo que inativa),
     * para não perder a seleção salva na edição e no recebimento.
     */
    private function formasPagamentoDisponiveis(Recebimento $recebimento)
    {
        return FormaPagamento::query()
            ->where('status', true)
            ->orWhere('id', $recebimento->forma_pagamento_id)
            ->orderBy('nome')
            ->get(['id', 'nome']);
    }

    /**
     * Mensagem para vendas cujo status não permite lançar recebimentos.
     */
    private function motivoSemLancamento(Venda $venda): string
    {
        return match ($venda->status) {
            Venda::STATUS_ABERTA => 'A venda está aberta. Lance os recebimentos pelo PDV ou finalize a venda.',
            Venda::STATUS_QUITADA => 'A venda já está quitada.',
            Venda::STATUS_CANCELADA => 'Não é possível lançar recebimentos em uma venda cancelada.',
            default => 'Esta venda não permite lançar recebimentos.',
        };
    }
}
