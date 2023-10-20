<?php

namespace App\Jobs;

use App\Events\ChangeDanaStatus;
use App\Models\DanaProductTransfer;
use App\Services\DanaProductService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DanaEditCourseJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $queue;
    private $product;
    private $extraDto;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product, $extraDto = [])
    {
        $this->queue = 'default2';
        $this->product = $product;
        $this->extraDto = $extraDto;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $danaProduct = DanaProductTransfer::where('product_id', $this->product->id)->get();
            if ($danaProduct->isEmpty()) {
                Log::channel('danaTransfer')->debug("In DanaProductService: editCourse : product {$this->product->id} has no corresponding ID in Dana");
                throw new Exception("In DanaEditCourseJob : product {$this->product->id} has no corresponding ID in Dana");
            }
            $danaProduct = $danaProduct->first();
            DanaProductService::editCourse($this->product, $danaProduct->dana_course_id, $this->extraDto);
            ChangeDanaStatus::dispatch($danaProduct->dana_course_id);
        } catch (Exception $exception) {
            Log::channel('danaTransfer')->debug('In DanaEditCourseJob exception : '.$exception->getMessage());
        }


    }
}
