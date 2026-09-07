<?php

namespace App\Relatorios\Contratos;

interface ExportadorInterface
{
    public function exportar($relatorio, array $filtros);
}
