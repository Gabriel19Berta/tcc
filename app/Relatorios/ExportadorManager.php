<?php
namespace App\Relatorios;

use App\Services\Exportadores\ExcelExportador;
use App\Services\Exportadores\PdfExportador;

class ExportadorManager
{
    public function obter(string $formato)
    {
        return match ($formato) {

            'excel' => app(ExcelExportador::class),

            'pdf' => app(PdfExportador::class),

            default => throw new \Exception('Formato inválido'),

        };
    }
}