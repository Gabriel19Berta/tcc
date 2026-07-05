<?php

namespace App\Relatorios\TipoProdutos;

use App\Models\TipoProduto;
use App\Relatorios\Contratos\RelatorioInterface;

class TipoProdutosRelatorio implements RelatorioInterface
{
    public function titulo(): string
    {
        return 'Listagem Tipo De Produtos';
    }

    public function modulo(): string
    {
        return 'Tipo Produtos';
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
            ->when(!empty($filtros['nome']),
                fn($query) => $query->where(
                    'nome',
                    'like',
                    "%{$filtros['nome']}%"
                )
            )
            ->orderBy('nome')
            ->get();
    }
}
