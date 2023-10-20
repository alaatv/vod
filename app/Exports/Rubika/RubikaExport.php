<?php

namespace App\Exports\Rubika;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RubikaExport implements WithMultipleSheets
{

    public function __construct(private Collection $sets)
    {
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->sets as $set) {
            $sheets[] = new RubikaExportContentsSheet($set);
        }

//        $sheets[] = new RubikaExportSetsSheet($this->sets);

        return $sheets;
    }


}
