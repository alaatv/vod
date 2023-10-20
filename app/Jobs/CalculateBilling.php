<?php

namespace App\Jobs;

use App\Services\BillingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateBilling implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private $orderId)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BillingService $billingService)
    {
        $billingService->fillBillingTable($this->orderId);
        $billingService->calculateOPshareAmountForBillingTable($this->orderId[0]);
    }
}
