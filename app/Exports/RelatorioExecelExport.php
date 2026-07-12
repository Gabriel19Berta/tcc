<?php

namespace App\Exports;

use App\Relatorios\Contratos\RelatorioInterface;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RelatorioExecelExport implements 
    FromCollection, 
    WithStyles, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize
{
    public function __construct(
        private Collection $dados,
        private RelatorioInterface $relatorio
    ) {}

    public function collection()
    {
        return $this->dados;
    }

    public function headings(): array
    {
        return $this->relatorio->cabecalhos();
    }

    public function map($row): array
    {
        return $this->relatorio->mapear($row);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
