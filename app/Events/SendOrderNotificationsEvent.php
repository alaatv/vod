<?php

namespace App\Events;

use App\Models\Order;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendOrderNotificationsEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $myOrder;
    public $user;
    public $notificationsBeSent;
    public $sync;

    /**
     * Create a new event instance.
     * SendOrderNotifications constructor.
     *
     * @param  Order|null  $myOrder
     * @param  User|null  $user
     * @param  bool  $notificationsBeSent
     */
    public function __construct(?Order $myOrder, ?User $user, bool $notificationsBeSent = false, bool $sync = false)
    {
        $this->myOrder = $myOrder;
        $this->user = $user;
        $this->notificationsBeSent = $notificationsBeSent;
        $this->sync = $sync;
    }
}
