<?php

namespace App\Events;

use App\Models\Content;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContentRedirected
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $content;

    /**
     * Create a new event instance.
     *
     * @param  Content  $content
     */
    public function __construct(Content $content)
    {
        $this->content = $content;
    }
}
