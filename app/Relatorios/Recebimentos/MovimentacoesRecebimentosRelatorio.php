<?php

namespace App\Relatorios\Recebimentos;

use App\Models\Estorno;
use App\Models\FormaPagamento;
use App\Models\Pagamento;
use App\Relatorios\Contratos\RelatorioInterface;
use App\Support\Formatador;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Override;

/**
 * Histórico financeiro dos recebimentos: cada pagamento (tabela pagamentos)
 * e cada estorno (tabela estornos) é uma linha, com a data real da operação.
 * Pagamentos desfeitos por um estorno continuam listados como pagamentos.
 */
class MovimentacoesRecebimentosRelatorio implements RelatorioInterface
{
    public const TIPO_PAGAMENTO = 'pagamento';
    public const TIPO_ESTORNO = 'estorno';

    #[Override]
    public function cabecalhos(): array
    {
        return [
            'Número da venda',
            'Número do recebimento',
            'Cliente',
            'Tipo de movimentação',
            'Data e horário',
            'Forma de pagamento',
            'Valor',
        ];
    }

    #[Override]
    public function linha($movimentacao): array
    {
        $estorno = $movimentacao instanceof Estorno;
        $recebimento = $movimentacao->recebimento;

        // O estorno não tem forma própria: são as formas dos pagamentos que ele desfez.
        $forma = $estorno
            ? $movimentacao->pagamentos->pluck('formaPagamento.nome')->filter()->unique()->implode(', ')
            : $movimentacao->formaPagamento?->nome;

        return [
            $recebimento?->venda_id,
            $movimentacao->recebimento_id,
            $recebimento?->venda?->cliente?->pessoa?->nome ?? 'Sem cliente',
            $estorno ? 'Estorno' : 'Pagamento',
            Formatador::data($this->dataMovimentacao($movimentacao), 'd/m/Y H:i'),
            $forma ?: 'Não informada',
            Formatador::moeda($movimentacao->valor),
        ];
    }

    #[Override]
    public function titulo(): string
    {
        return 'Relatório de Movimentações de Recebimentos';
    }

    #[Override]
    public function modulo(): string
    {
        return 'Recebimentos';
    }

    #[Override]
    public function slug(): string
    {
        return 'movimentacoes-recebimentos';
    }

    #[Override]
    public function viewFiltros(): string
    {
        return 'relatorios.movimentacoes-recebimentos.filtros';
    }

    /**
     * Opções do filtro de forma de pagamento (todas, inclusive inativas,
     * pois podem ter sido usadas em recebimentos antigos).
     */
    public function formasPagamento()
    {
        return FormaPagamento::query()->orderBy('nome')->get(['id', 'nome']);
    }

    #[Override]
    public function gerar(array $filtros)
    {
        $filtros = Validator::make($filtros, [
            'data-inicio' => ['nullable', 'date'],
            'data-fim' => ['nullable', 'date'],
            'cliente' => ['nullable', 'string', 'max:100'],
            'forma_pagamento_id' => ['nullable', 'integer'],
            'tipo' => ['nullable', 'in:' . self::TIPO_PAGAMENTO . ',' . self::TIPO_ESTORNO],
        ], [
            'tipo.in' => 'O tipo de movimentação selecionado é inválido.',
        ])->validate();

        // Período sobre a data e o horário da operação (dia inicial inteiro até o fim do dia final).
        $inicio = filled($filtros['data-inicio'] ?? null) ? Carbon::parse($filtros['data-inicio'])->startOfDay() : null;
        $fim = filled($filtros['data-fim'] ?? null) ? Carbon::parse($filtros['data-fim'])->endOfDay() : null;
        $cliente = $filtros['cliente'] ?? null;
        $formaId = $filtros['forma_pagamento_id'] ?? null;
        $tipo = $filtros['tipo'] ?? null;

        $pagamentos = $tipo === self::TIPO_ESTORNO ? collect() : Pagamento::query()
            ->with(['recebimento.venda.cliente.pessoa', 'formaPagamento'])
            ->when($inicio, fn ($query) => $query->where('data_pagamento', '>=', $inicio))
            ->when($fim, fn ($query) => $query->where('data_pagamento', '<=', $fim))
            ->when($cliente, fn ($query) => $query->whereHas(
                'recebimento.venda.cliente.pessoa',
                fn ($pessoa) => $pessoa->filtroBusca($cliente)
            ))
            ->when($formaId, fn ($query) => $query->where('forma_pagamento_id', $formaId))
            ->get();

        $estornos = $tipo === self::TIPO_PAGAMENTO ? collect() : Estorno::query()
            ->with(['recebimento.venda.cliente.pessoa', 'pagamentos.formaPagamento'])
            ->when($inicio, fn ($query) => $query->where('data_estorno', '>=', $inicio))
            ->when($fim, fn ($query) => $query->where('data_estorno', '<=', $fim))
            ->when($cliente, fn ($query) => $query->whereHas(
                'recebimento.venda.cliente.pessoa',
                fn ($pessoa) => $pessoa->filtroBusca($cliente)
            ))
            // Estorno de pagamentos feitos com a forma escolhida.
            ->when($formaId, fn ($query) => $query->whereHas(
                'pagamentos',
                fn ($pagamento) => $pagamento->where('forma_pagamento_id', $formaId)
            ))
            ->get();

        // Mais recente primeiro. No mesmo horário, o estorno vem antes dos
        // pagamentos (ele sempre ocorre depois dos pagamentos que desfaz).
        return $pagamentos->toBase()
            ->merge($estornos)
            ->sortBy([
                fn ($a, $b) => $this->dataMovimentacao($b) <=> $this->dataMovimentacao($a),
                fn ($a, $b) => ($b instanceof Estorno) <=> ($a instanceof Estorno),
                fn ($a, $b) => $b->id <=> $a->id,
            ])
            ->values();
    }

    private function dataMovimentacao($movimentacao): ?Carbon
    {
        return $movimentacao instanceof Estorno
            ? $movimentacao->data_estorno
            : $movimentacao->data_pagamento;
    }
}
