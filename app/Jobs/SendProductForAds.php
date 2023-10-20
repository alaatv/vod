<?php

namespace App\Jobs;

use App\Classes\Marketing\Yektanet\Yektanet;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendProductForAds implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Product $product;
    private Yektanet $yektanet;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->yektanet = new Yektanet();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->yektanet->sendSingleProduct($this->product);
    }
}
