<?php

namespace App\Jobs;

use App\Models\BatchContentInsert;
use App\Models\BatchContentInsert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Log;

class BulkInsertContents implements ShouldQueue
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
    public function __construct(public string $fileName, public int $productId, public int $insertId)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('alaaTv:add-contents '.$this->fileName.' '.$this->productId.' '.$this->insertId);
    }

    public function failed($exception)
    {
        BatchContentInsert::find($this->insertId)->update([
            'status' => 'failed',
        ]);
        Log::error($exception->getMessage());
    }
}
