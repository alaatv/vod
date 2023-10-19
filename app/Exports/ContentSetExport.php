<?php

namespace App\Exports;

use App\Models\Content;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class ContentSetExport implements WithMultipleSheets, WithTitle, FromQuery, WithHeadings, WithEvents, WithColumnWidths
{
    use Exportable;

    protected $id;

    public function __construct(int $id = 0)
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $ids = [
            1617,
            1618,
            1619,
            1620,
            1622,
            1623,
            1624,
            1625,
            1626,
            1627,
            1628,
            1629,
            1630,
            1631,
            1649,
            1732,
            1761,
            1701,
            1702,
            1703,
            1704,
            1706,
            1707,
            1708,
            1709,
            1710,
            1711,
            1712,
            1713,
            1714,
            1715,
            1721,
        ];
        foreach ($ids as $id) {
            $sheets[] = new ContentSetExport($id);
        }
        return $sheets;
    }

    public function title(): string
    {
        return $this->id;
    }

    public function headings(): array
    {
        return [
            'نام',
            'ترتیب',
            'مدت زمان'
        ];
    }

    public function query()
    {
        return Content::where('contentset_id', $this->id)
            ->where('contenttype_id', '=', 8)
            ->select(['name', 'order', 'duration']);
    }

    /**
     * @inheritDoc
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 80,
            'B' => 10,
        ];
    }
}
