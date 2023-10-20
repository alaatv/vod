<?php

namespace App\Jobs;

use App\Services\DanaProductService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DanaProductTransferJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $queue;
    private $danaProductId;
    private $set;
    private $order;
    private $productId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($danaProductId, $set, $order, $productId)
    {
        $this->queue = 'default3';
        $this->danaProductId = $danaProductId;
        $this->set = $set;
        $this->order = $order;
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            DanaProductService::createSession($this->danaProductId, $this->set, $this->order, $this->productId);
        } catch (Exception $exception) {

        }
    }
}
