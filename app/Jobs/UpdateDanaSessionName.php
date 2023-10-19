<?php

namespace App\Jobs;

use App\Events\ChangeDanaStatus;
use App\Services\DanaProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateDanaSessionName implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $queue;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected int $danaSessionId, protected string $setSmallName, protected $danaCourseId)
    {
        $this->queue = 'default2';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DanaProductService $danaProductService)
    {
        $danaProductService::updateSessionName([
            'sessionId' => $this->danaSessionId,
            'name' => $this->setSmallName,
        ]);
        ChangeDanaStatus::dispatch($this->danaCourseId);
    }
}
