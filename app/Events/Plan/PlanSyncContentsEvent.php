<?php

namespace App\Events\Plan;

use App\Models\Plan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlanSyncContentsEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var array
     */
    public $contents;
    /**
     * @var Plan
     */
    public $plan;

    /**
     * Create a new event instance.
     *
     * @param  Plan  $plan
     * @param  array  $contents
     */
    public function __construct(Plan $plan, array $contents)
    {

        $this->contents = $contents;

        $this->plan = $plan;
    }


}
