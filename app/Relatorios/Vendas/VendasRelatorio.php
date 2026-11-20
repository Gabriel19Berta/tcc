<?php

namespace App\Relatorios\Vendas;

use App\Models\Venda;
use App\Relatorios\Contratos\RelatorioInterface;
use App\Support\Formatador;
use Illuminate\Support\Facades\Validator;
use Override;

class VendasRelatorio implements RelatorioInterface
{
    public const STATUS = [
        Venda::STATUS_ABERTA,
        Venda::STATUS_FINALIZADA,
        Venda::STATUS_QUITADA,
        Venda::STATUS_CANCELADA,
    ];

    #[Override]
    public function cabecalhos(): array
    {
        return [
            'Número da venda',
            'Data da venda',
            'Cliente',
            'Funcionário responsável',
            'Valor dos produtos',
            'Descontos',
            'Outros custos',
            'Valor total',
            'Status',
        ];
    }

    #[Override]
    public function linha($venda): array
    {
        // Valores gravados na venda (calculados pelo PDV), sem recalcular pelos itens.
        return [
            $venda->id,
            Formatador::data($venda->data, 'd/m/Y H:i'),
            $venda->cliente?->pessoa?->nome ?? 'Sem cliente',
            $venda->funcionario?->pessoa?->nome ?? 'Não informado',
            Formatador::moeda($venda->valor_produtos),
            Formatador::moeda($venda->descontos),
            Formatador::moeda($venda->outros_custos),
            Formatador::moeda($venda->valor_total),
            ucfirst($venda->status),
        ];
    }

    #[Override]
    public function titulo(): string
    {
        return 'Relatório de Vendas';
    }

    #[Override]
    public function modulo(): string
    {
        return 'Vendas';
    }

    #[Override]
    public function slug(): string
    {
        return 'vendas';
    }

    #[Override]
    public function viewFiltros(): string
    {
        return 'relatorios.vendas.filtros';
    }

    #[Override]
    public function gerar(array $filtros)
    {
        $filtros = Validator::make($filtros, [
            'data-inicio' => ['nullable', 'date'],
            'data-fim' => ['nullable', 'date'],
            'cliente' => ['nullable', 'string', 'max:100'],
            'funcionario' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:' . implode(',', self::STATUS)],
        ], [
            'status.in' => 'O status selecionado é inválido.',
        ])->validate();

        // Uma linha por venda: os filtros de cliente/funcionário usam whereHas
        // (sem join), então itens e recebimentos não duplicam nem somam valores.
        return Venda::query()
            ->with(['cliente.pessoa', 'funcionario.pessoa'])
            ->when($filtros['data-inicio'] ?? null, fn ($query, $data) => $query->whereDate('data', '>=', $data))
            ->when($filtros['data-fim'] ?? null, fn ($query, $data) => $query->whereDate('data', '<=', $data))
            ->when($filtros['cliente'] ?? null, fn ($query, $cliente) => $query->whereHas(
                'cliente.pessoa',
                fn ($pessoa) => $pessoa->filtroBusca($cliente)
            ))
            ->when($filtros['funcionario'] ?? null, fn ($query, $funcionario) => $query->whereHas(
                'funcionario.pessoa',
                fn ($pessoa) => $pessoa->filtroBusca($funcionario)
            ))
            ->when($filtros['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('data')
            ->orderByDesc('id')
            ->get();
    }
}
