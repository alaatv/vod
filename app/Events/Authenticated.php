<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Authenticated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * The verified user.
     *
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  User  $user
     *
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
