<?php

namespace App\Relatorios\Contratos;

interface RelatorioInterface
{
    public function cabecalhos(): array;
    public function linha($registro): array;
    public function titulo(): string;
    public function modulo(): string;
    public function slug(): string;
    public function viewFiltros(): string;
    public function gerar(array $filtros);
}
