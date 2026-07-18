<?php

namespace App\Relatorios\Produtos;

use App\Models\Produto;
use App\Relatorios\Contratos\RelatorioInterface;
use App\Support\Formatador;
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
    public function linha($produto): array
    {
        return [
            $produto->id,
            Formatador::status($produto->status),
            $produto->nome,
            $produto->marca?->nome,
            $produto->tipoProduto?->nome,
            $produto->peso,
            Formatador::moeda($produto->preco_custo),
            Formatador::moeda($produto->preco_venda),
            $produto->controla_estoque ? 'Sim' : 'Não',
            $produto->quantidade,
            Formatador::data($produto->created_at),
            Formatador::data($produto->update_at)
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
        return Produto::with(['marca', 'tipoProduto'])
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
