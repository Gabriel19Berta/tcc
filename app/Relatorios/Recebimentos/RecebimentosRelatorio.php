<?php

namespace App\Relatorios\Recebimentos;

use App\Models\FormaPagamento;
use App\Models\Recebimento;
use App\Relatorios\Contratos\RelatorioInterface;
use App\Support\Formatador;
use Illuminate\Support\Facades\Validator;
use Override;

class RecebimentosRelatorio implements RelatorioInterface
{
    #[Override]
    public function cabecalhos(): array
    {
        return [
            'Número do recebimento',
            'Número da venda',
            'Cliente',
            'Forma de pagamento',
            'Valor original',
            'Valor recebido',
            'Saldo pendente',
            'Vencimento',
            'Status',
        ];
    }

    #[Override]
    public function linha($recebimento): array
    {
        // Mesmas regras da tela do recebimento (Model Recebimento): recebido =
        // pagamentos ainda não estornados; saldo = valor original − recebido.
        $recebidoCentavos = $recebimento->totalRecebidoEmCentavos();

        // Cancelado não tem saldo disponível para pagamento.
        $saldoCentavos = $recebimento->status === Recebimento::STATUS_CANCELADO
            ? 0
            : max(0, $recebimento->saldoEmCentavos());

        return [
            $recebimento->id,
            $recebimento->venda_id,
            $recebimento->venda?->cliente?->pessoa?->nome ?? 'Sem cliente',
            $recebimento->formaPagamento?->nome ?? 'Não informada',
            Formatador::moeda($recebimento->valor),
            Formatador::moeda($recebidoCentavos / 100),
            Formatador::moeda($saldoCentavos / 100),
            Formatador::data($recebimento->data_vencimento, 'd/m/Y'),
            ucfirst($recebimento->status),
        ];
    }

    #[Override]
    public function titulo(): string
    {
        return 'Relatório de Recebimentos';
    }

    #[Override]
    public function modulo(): string
    {
        return 'Recebimentos';
    }

    #[Override]
    public function slug(): string
    {
        return 'recebimentos';
    }

    #[Override]
    public function viewFiltros(): string
    {
        return 'relatorios.recebimentos.filtros';
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
            'status' => ['nullable', 'in:' . implode(',', Recebimento::STATUS)],
        ], [
            'status.in' => 'O status selecionado é inválido.',
        ])->validate();

        // Uma linha por recebimento. Os pagamentos são carregados de uma vez
        // (eager loading) apenas para o cálculo do valor recebido, sem duplicar linhas.
        return Recebimento::query()
            ->with(['venda.cliente.pessoa', 'formaPagamento', 'pagamentos'])
            ->when($filtros['data-inicio'] ?? null, fn ($query, $data) => $query->whereDate('data_vencimento', '>=', $data))
            ->when($filtros['data-fim'] ?? null, fn ($query, $data) => $query->whereDate('data_vencimento', '<=', $data))
            ->when($filtros['cliente'] ?? null, fn ($query, $cliente) => $query->whereHas(
                'venda.cliente.pessoa',
                fn ($pessoa) => $pessoa->filtroBusca($cliente)
            ))
            ->when($filtros['forma_pagamento_id'] ?? null, fn ($query, $formaId) => $query->where('forma_pagamento_id', $formaId))
            ->when($filtros['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('data_vencimento')
            ->orderBy('id')
            ->get();
    }
}
