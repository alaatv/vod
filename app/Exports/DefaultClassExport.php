<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;

/**
 * Note: We must use the WithStrictNullComparison class to store 0 instead of the empty space in returned Excel.
 * Note: We must use the WithStrictNullComparison class to fire the registerEvents method. This method modifies the returned Excel as rtl.
 */
class DefaultClassExport implements FromCollection, WithHeadings, WithStrictNullComparison, WithEvents
{
    private $collection;
    private $columns;

    /**
     * SaleReportExport constructor.
     *
     * @param  Collection  $collection
     * @param  array  $columns
     */
    public function __construct(Collection $collection, array $columns)
    {
        $this->collection = $collection;
        $this->columns = $columns;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}
