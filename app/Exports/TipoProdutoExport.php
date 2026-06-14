<?php

namespace App\Exports;

use App\Models\TipoProduto;
use Maatwebsite\Excel\Concerns\FromCollection;

class TipoProdutoExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return TipoProduto::all();
    }
}
