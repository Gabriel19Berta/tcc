<?php

namespace App\Exports;

use App\Models\TipoProduto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class TipoProdutoExport implements FromCollection, WithStyles, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return TipoProduto::all();
    }

    public function styles(Worksheet $sheet) {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }

    public function headings(): array
    {
        return [
            'Cód',
            'Situação',
            'Nome',
            'Criado em',
            'Modificado em'
        ];
    }

    public function map($tipoProduto): array
    {
        $statusFormatado = $tipoProduto->status == 1 ? 'Ativo' : 'Inativo' ;

        return [
            $tipoProduto->id,
            $statusFormatado,
            $tipoProduto->nome,
            $tipoProduto->created_at->format('d/m/Y H:m:s'),
            $tipoProduto->updated_at->format('d/m/Y H:m:s')
        ];
    }
}
