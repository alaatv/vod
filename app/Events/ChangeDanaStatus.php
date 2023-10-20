<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChangeDanaStatus
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $courseId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($courseId)
    {
        $this->courseId = $courseId;
    }
}
