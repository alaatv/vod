<?php

namespace App\Exports\Rubika;

use App\Models\Contentset;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RubikaExportSetsSheet implements FromArray, WithHeadings
{
    public function __construct(private Collection $sets)
    {
    }

    public function array(): array
    {
        return $this->sets
            ->map(function (Contentset $set) {
                $reportableData = $this->makeupSet($set);
                return $reportableData;

            })->toArray();
    }

    private function makeupSet(Contentset $set)
    {
        $reportableData['name'] = $set->name;
        $reportableData['thumbnail'] = $set->photo;
        return $reportableData;
    }

    public function headings(): array
    {
        return ['عنوان', 'عکس تامبنیل'];
    }
}
