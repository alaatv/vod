<?php

namespace App\Jobs;

use App\Events\ChangeDanaStatus;
use App\Models\DanaProductSetTransfer;
use App\Models\DanaProductTransfer;
use App\Services\DanaProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateDanaSessionOrder implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected int $productId;
    protected array $orders;
    protected int $danaCourseId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $productId, array $orders)
    {
        $this->productId = $productId;
        $this->orders = $orders;
        $this->danaCourseId = DanaProductTransfer::where('product_id', $this->productId)->first()->dana_course_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DanaProductService $danaProductService)
    {
        $priorityDtos = [];
        foreach ($this->orders as $setId => $order) {
            $danaSession =
                DanaProductSetTransfer::where('product_id', $this->productId)->where('contentset_id', $setId)->first();
            if (!is_null($danaSession)) {
                $order = (int) $order;
                $order = $order == 0 ? 1 : $order;
                $priorityDtos[] = [
                    'Id' => $danaSession->dana_session_id,
                    'Priority' => $order,
                ];
            }
        }
        $priorityDtos = array_chunk($priorityDtos, 4);
        foreach ($priorityDtos as $priorityDto) {
            $danaProductService::updateSessionPriority($priorityDto);
        }
        ChangeDanaStatus::dispatch($this->danaCourseId);
    }
}
