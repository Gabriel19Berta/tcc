<?php

namespace App\Relatorios\Marcas;

use App\Models\Marca;
use App\Relatorios\Contratos\RelatorioInterface;

class MarcasRelatorio implements RelatorioInterface
{
    public function titulo(): string
    {
        return 'Listagem Marcas';
    }

    public function modulo(): string
    {
        return 'Marcas';
    }

    public function slug(): string
    {
        return 'marcas';
    }

    public function viewFiltros(): string
    {
        return 'relatorios.marcas.filtros';
    }

    public function gerar(array $filtros)
    {
        return Marca::query()
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
