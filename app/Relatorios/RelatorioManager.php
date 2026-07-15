<?php

namespace App\Relatorios;

use App\Relatorios\Marcas\MarcasRelatorio;
use App\Relatorios\TipoProdutos\TipoProdutosRelatorio;
use App\Relatorios\Produtos\ProdutosRelatorio;

class RelatorioManager
{
    protected array $relatorios = [
        MarcasRelatorio::class,
        TipoProdutosRelatorio::class,
        ProdutosRelatorio::class,
    ];

    public function todos()
    {
        return collect($this->relatorios)->map(fn ($classe) => app($classe));
    }

    public function buscar(string $slug)
    {
        return $this->todos()
            ->first(fn ($relatorio) => $relatorio->slug() === $slug);
    }

    public function modulos()
    {
        return $this->todos()
            ->groupBy(fn ($relatorio) => $relatorio->modulo());
    }
}
