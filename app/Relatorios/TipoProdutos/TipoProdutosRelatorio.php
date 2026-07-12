<?php

namespace App\Relatorios\TipoProdutos;

use App\Models\TipoProduto;
use App\Relatorios\Contratos\RelatorioInterface;
use Override;

class TipoProdutosRelatorio implements RelatorioInterface
{
    #[Override]
    public function cabecalhos(): array
    {
        return [
            'Código',
            'Status',
            'Nome',
            'Criado em',
            'Modificado em'
        ];
    }

    #[Override]
    public function mapear($tipoProduto): array
    {
        return [
            $tipoProduto->id,
            $tipoProduto->status ? 'Ativo' : 'Inativo',
            $tipoProduto->nome,
            $tipoProduto->created_at->format('d/m/Y H:i:s'),
            $tipoProduto->update_at ? $tipoProduto->update_at->format('d/m/Y H:i:s') : ''
        ];
    }

    public function titulo(): string
    {
        return 'Listagem Tipo De Produtos';
    }

    public function modulo(): string
    {
        return 'Tipo de Produtos';
    }

    public function slug(): string
    {
        return 'tipo-produtos';
    }

    public function viewFiltros(): string
    {
        return 'relatorios.tipo-produtos.filtros';
    }

    public function gerar(array $filtros)
    {
        return TipoProduto::query()
            ->when(filled($filtros['status']),
                fn($query) => $query->where(
                    'status',
                    '=',
                    $filtros['status']
                )
            )
            ->when(!empty($filtros['data-inicio']),
                fn($query) => $query->whereDate(
                    'created_at',
                    '>=',
                    $filtros['data-inicio']
                )
            )
            ->when(!empty($filtros['data-fim']),
                fn($query) => $query->whereDate(
                    'updated_at',
                    '<=',
                    $filtros['data-fim']
                )
            )
            ->orderBy('nome')
            ->get();
    }
}
