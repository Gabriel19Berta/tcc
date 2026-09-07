<?php

namespace App\Relatorios\Marcas;

use App\Models\Marca;
use App\Relatorios\Contratos\RelatorioInterface;
use App\Support\Formatador;
use Override;

class MarcasRelatorio implements RelatorioInterface
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
    public function linha($marca): array
    {
        return [
            $marca->id,
            Formatador::status($marca->status),
            $marca->nome,
            Formatador::data($marca->created_at),
            Formatador::data($marca->update_at)
        ];
    }

    #[Override]
    public function titulo(): string
    {
        return 'Listagem Marcas';
    }

    #[Override]
    public function modulo(): string
    {
        return 'Marcas';
    }

    #[Override]
    public function slug(): string
    {
        return 'marcas';
    }

    #[Override]
    public function viewFiltros(): string
    {
        return 'relatorios.marcas.filtros';
    }

    #[Override]
    public function gerar(array $filtros)
    {
        return Marca::query()
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
