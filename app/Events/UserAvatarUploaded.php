<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class UserAvatarUploaded
{

    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $path;
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  User  $user
     * @param  string  $path
     */
    public function __construct(User $user, string $path)
    {
        $this->user = $user;
        $this->path = $path;
    }
}
