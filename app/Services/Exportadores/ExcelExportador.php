<?php
namespace App\Services\Exportadores;

use App\Exports\RelatorioExcelExport;
use App\Exports\RelatorioExecelExport;
use App\Relatorios\Contratos\ExportadorInterface;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportador implements ExportadorInterface
{
    public function exportar($relatorio, array $filtros)
    {
        $dados = $relatorio->gerar($filtros);

        return Excel::download(
            new RelatorioExecelExport($dados, $relatorio),

            $relatorio->slug().'.xlsx'
        );
    }
}