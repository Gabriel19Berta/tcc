<?php

namespace App\Relatorios\Produtos;

use App\Models\Produto;
use App\Relatorios\Contratos\RelatorioInterface;
use Override;

class ProdutosRelatorio implements RelatorioInterface
{
    #[Override]
    public function cabecalhos(): array
    {
        return [
            'Código',
            'Status',
            'Nome',
            'Marca',
            'Tipo de Produto',
            'Peso',
            'Preço de Custo',
            'Preço de Venda',
            'Contrala Estoque',
            'Quantidade estoque',
            'Criado em',
            'Modificado em'
        ];
    }

    #[Override]
    public function mapear($produto): array
    {
        return [
            $produto->id,
            $produto->status ? 'Ativo' : 'Inativo',
            $produto->nome,
            $produto->marca_id,
            $produto->tipo_produto_id,
            $produto->peso,
            $produto->preco_custo,
            $produto->preco_venda,
            $produto->contra_estoque ? 'Inativo' : 'Ativo',
            $produto->quantidade,
            $produto->created_at->format('d/m/Y H:i:s'),
            $produto->update_at ? $produto->update_at->format('d/m/Y H:i:s') : ''
        ];
    }

    #[Override]
    public function titulo(): string
    {
        return 'Listagem Produtos';
    }

    #[Override]
    public function modulo(): string
    {
        return 'Produtos';
    }

    #[Override]
    public function slug(): string
    {
        return 'produtos';
    }

    #[Override]
    public function viewFiltros(): string
    {
        return 'relatorios.produtos.filtros';
    }

    #[Override]
    public function gerar(array $filtros)
    {
        return Produto::query()
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
            ->get();
    }
}
