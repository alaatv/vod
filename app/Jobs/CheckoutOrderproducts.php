<?php

namespace App\Jobs;

use App\Models\Orderproduct;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CheckoutOrderproducts implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $timeout = 105;

    /**
     * @var User
     */
    private $orderproducts;

    /**
     * ExportSaleReportExcel constructor.
     *
     * @param  Collection  $orderproudcts
     */
    public function __construct($orderproudcts)
    {
        $this->queue = 'default2';
        $this->orderproducts = $orderproudcts;
    }


    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        Orderproduct::whereIn('id',
            $this->orderproducts->pluck('id'))->update(['checkoutstatus_id' => config('constants.ORDERPRODUCT_CHECKOUT_STATUS_PAID')]);
    }
}
