<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;


class InvoiceExport implements FromView
{
    use Exportable;
    use RegistersEventListeners;

    public function __construct(private array $rows, private array $footer, private array $header)
    {
    }

    public function view(): View
    {
        return view('admin.auditReport', ['rows' => $this->rows, 'footer' => $this->footer, 'header' => $this->header]);
    }
}
