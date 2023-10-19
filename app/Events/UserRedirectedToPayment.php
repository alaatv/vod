<?php

namespace App\Events;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRedirectedToPayment
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $user;
    public $order;
    public $transaction;

    /**
     * Create a new event instance.
     *
     * @param  User  $user
     * @param  Order|null  $order
     * @param  Transaction|null  $transaction
     */
    public function __construct(?User $user, ?Order $order = null, ?Transaction $transaction = null)
    {
        $this->user = $user;
        $this->order = $order;
        $this->transaction = $transaction;
    }
}
