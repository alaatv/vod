<?php

namespace App\Jobs;

use App\Classes\NumberToString;
use App\Models\FinancialCategory;
use App\Models\FinancialCategory;
use App\Models\Report;
use App\Models\Transactiongateway;
use App\Models\User;
use App\PaymentModule\Money;
use App\Repositories\BillingRepo;
use App\Traits\DateTrait;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FinancialAuditJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use DateTrait;

    private string $since;

    private string $till;

    private string $disk = 'reportsMinio';

    private array $invoiceFooterReport = [
        'invoice_total' => 0,
        'plus' => 0,
        'discount' => 0,
        'taxt' => 0,
        'payable' => 0,
        'payableString' => '',
    ];

    private array $invoiceHeaderReport = [
        'created_at' => 0,
        'since' => 0,
        'till' => 0,
        'gateway' => '',
        'invoiceId' => '',
    ];

    /**
     * @description  add you products financial category in $items array
     * @var array $financialCategoriesReport
     * */
    private array $financialCategoriesReport = [
        FinancialCategory::ONLINE_AUDIO_BOOK_SECOND_GRADE => [
            'total' => 0,
            'fee' => 0,
            'tax' => 0,
            'count' => 0,
            'description' => 'کتاب آنلاین گویای متوسطه دوم',
            'code' => '193',
        ],
        FinancialCategory::_3A => [
            'total' => 0,
            'fee' => 0,
            'tax' => 0,
            'count' => 0,
            'description' => 'سه آ',
            'code' => '-',
        ],
        3 => [
            'total' => 0,
            'fee' => 0,
            'tax' => 0,
            'count' => 0,
            'description' => 'کمک مالی',
            'code' => '-',
        ],
    ];

    public function __construct(private array $data, private User $user, private Report $report)
    {
        $this->setInterval();
    }

    private function setInterval(): void
    {
        $this->since = $this->data['from'];
        $this->till = $this->data['to'];
    }

    public function handle()
    {
        $billingInvoice = $this->getBillingInvoice();

        $this->prepareReportData($billingInvoice);

        $this->updateReport();

    }

    public function getBillingInvoice(): Collection
    {
        $gateway = is_array($this->data['gateway']) ? $this->data['gateway'] : [$this->data['gateway']];
        return BillingRepo::getInvoiceByDate($this->since, $this->till, $gateway);
    }

    private function prepareReportData($orderProducts): void
    {
        $this->setFinancialCategories($orderProducts);
        $this->eliminateZeroSeal();
        $this->setHeader();
        $this->setFooter();
    }

    private function setFinancialCategories(Collection $billingInvoices): void
    {
        foreach ($billingInvoices as $invoice) {
            $this->financialCategoriesReport[$invoice->op_f_category_id]['count'] = 1;
            $this->financialCategoriesReport[$invoice->op_f_category_id]['fee'] =
                Money::fromTomans($invoice->sum_f_category_cost)->rials();
//                Money::fromTomans($invoice->sum_f_category_cost / $invoice->op_count)->rials();
            $this->financialCategoriesReport[$invoice->op_f_category_id]['total'] =
                Money::fromTomans($invoice->sum_f_category_cost)->rials();
        }
    }

    private function eliminateZeroSeal()
    {
        foreach ($this->financialCategoriesReport as $type => $data) {
            if ($data['total'] != 0) {
                continue;
            }
            unset($this->financialCategoriesReport[$type]);
        }
    }

    private function setHeader(): void
    {
        $this->invoiceHeaderReport['created_at'] =
            $this->convertDate(Carbon::createFromFormat('Y-m-d', $this->till)->addDay()->toDateTimeString(),
                'toJalali');
        $this->invoiceHeaderReport['since'] = $this->convertDate($this->since, 'toJalali');
        $this->invoiceHeaderReport['till'] = $this->convertDate($this->till, 'toJalali');
        $this->invoiceHeaderReport['invoiceId'] = $this->setInvoiceId();
        $this->invoiceHeaderReport['gateway'] = $this->setGateway();
    }

    private function setInvoiceId(): string
    {
        $createdAt = Carbon::parse($this->report->getRawOriginal('created_at'))->timestamp;
        $splitedCreatedAt = str_split($createdAt, 5);
        $createdAt = implode('-', $splitedCreatedAt);

        $reportId = str_pad($this->report->id, 4, '0', STR_PAD_LEFT);

        $prefix = 'INVO';

        return "$prefix-$createdAt-$reportId";
    }

    private function setGateway()
    {
        return Transactiongateway::find(Arr::get($this->data, 'gateway'))?->displayName;
    }

    private function setFooter(): void
    {
        foreach ($this->financialCategoriesReport as $item) {
            $this->invoiceFooterReport['invoice_total'] += $item['total'];
        }

        $this->invoiceFooterReport['plus'] = 0;
        $this->invoiceFooterReport['discount'] = 0;
        $this->invoiceFooterReport['taxt'] = 0;
        $this->invoiceFooterReport['payable'] = $this->invoiceFooterReport['invoice_total'];
        $this->invoiceFooterReport['payableString'] = NumberToString::convertToRial($this->invoiceFooterReport['invoice_total']);
    }

    private function updateReport()
    {
        $this->report->update([
            'data' => [
                'rows' => $this->financialCategoriesReport,
                'footer' => $this->invoiceFooterReport,
                'header' => $this->invoiceHeaderReport,
                'gateway_id' => Arr::get($this->data, 'gateway')
            ],
            'status_id' => config('constants.REPORT_STATUS_CREATED'),
            'file' => null,
        ]);
    }
}
