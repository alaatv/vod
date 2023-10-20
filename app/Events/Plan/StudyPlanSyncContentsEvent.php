<?php

namespace App\Events\Plan;

use App\Models\Plan;
use App\Models\Studyplan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudyPlanSyncContentsEvent
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
    public $studyPlan;

    /**
     * Create a new event instance.
     *
     * @param  Studyplan  $studyPlan
     * @param  array  $contents
     */
    public function __construct(Studyplan $studyPlan, array $contents)
    {

        $this->contents = $contents;

        $this->studyPlan = $studyPlan;
    }


}
