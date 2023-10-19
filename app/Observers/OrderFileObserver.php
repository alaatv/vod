<?php

namespace App\Observers;

use App\Models\Orderfile;

class OrderFileObserver
{
    /**
     * Handle the Orderfile "created" event.
     *
     * @param  Orderfile  $orderfile
     * @return void
     */
    public function created(Orderfile $orderfile)
    {
        //
    }

    /**
     * Handle the Orderfile "updated" event.
     *
     * @param  Orderfile  $orderfile
     * @return void
     */
    public function updated(Orderfile $orderfile)
    {
        //
    }

    /**
     * Handle the Orderfile "deleted" event.
     *
     * @param  Orderfile  $orderfile
     * @return void
     */
    public function deleted(Orderfile $orderfile)
    {
        //
    }

    /**
     * Handle the Orderfile "restored" event.
     *
     * @param  Orderfile  $orderfile
     * @return void
     */
    public function restored(Orderfile $orderfile)
    {
        //
    }

    /**
     * Handle the Orderfile "force deleted" event.
     *
     * @param  Orderfile  $orderfile
     * @return void
     */
    public function forceDeleted(Orderfile $orderfile)
    {
        //
    }

    /**
     * Handle the Orderfile "creating" and "updating" event.
     *
     * @param  Orderfile  $orderfile
     */
    public function saving(Orderfile $orderfile)
    {
        $orderfile->user_id = auth()->id();
    }
}
