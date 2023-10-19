<?php

namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;

class TransactionObserver
{
    /**
     * Handle the transaction "created" event.
     *
     * @param  Transaction  $transaction
     *
     * @return void
     */
    public function created(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "updated" event.
     *
     * @param  Transaction  $transaction
     *
     * @return void
     */
    public function updated(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "deleted" event.
     *
     * @param  Transaction  $transaction
     *
     * @return void
     */
    public function deleted(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "restored" event.
     *
     * @param  Transaction  $transaction
     *
     * @return void
     */
    public function restored(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "force deleted" event.
     *
     * @param  Transaction  $transaction
     *
     * @return void
     */
    public function forceDeleted(Transaction $transaction)
    {
        //
    }

    public function saved(Transaction $transaction)
    {
        Cache::tags(['transaction_'.$transaction->id, 'order_'.$transaction->order_id])->flush();
    }
}
