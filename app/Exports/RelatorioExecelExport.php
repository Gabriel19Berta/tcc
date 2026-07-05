<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class RelatorioExecelExport implements FromCollection
{
    public function __construct(
        protected Collection $collection
    ) {}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(): Collection
    {
        return $this->collection;
    }
}
