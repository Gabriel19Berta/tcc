<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MarcasExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected Collection $dados
    ) {}

    public function collection()
    {
        return $this->dados->map(function ($marca) {
            return [
                $marca->id,
                $marca->nome,
                $marca->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Código',
            'Descrição',
            'Cadastro',
        ];
    }
}