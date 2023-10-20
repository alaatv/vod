<?php

namespace App\Observers;

use App\Models\Orderpostinginfo;

class OrderPostingInfoObserver
{
    /**
     * Handle the orderpostinginfo "created" event.
     *
     * @param  Orderpostinginfo  $orderpostinginfo
     * @return void
     */
    public function created(Orderpostinginfo $orderpostinginfo)
    {
        //
    }

    /**
     * Handle the orderpostinginfo "updated" event.
     *
     * @param  Orderpostinginfo  $orderpostinginfo
     * @return void
     */
    public function updated(Orderpostinginfo $orderpostinginfo)
    {
        //
    }

    /**
     * Handle the orderpostinginfo "deleted" event.
     *
     * @param  Orderpostinginfo  $orderpostinginfo
     * @return void
     */
    public function deleted(Orderpostinginfo $orderpostinginfo)
    {
        //
    }

    /**
     * Handle the orderpostinginfo "restored" event.
     *
     * @param  Orderpostinginfo  $orderpostinginfo
     * @return void
     */
    public function restored(Orderpostinginfo $orderpostinginfo)
    {
        //
    }

    /**
     * Handle the orderpostinginfo "force deleted" event.
     *
     * @param  Orderpostinginfo  $orderpostinginfo
     * @return void
     */
    public function forceDeleted(Orderpostinginfo $orderpostinginfo)
    {
        //
    }

    /**
     * Handle the Orderpostinginfo "creating" and "updating" event.
     *
     * @param  Orderpostinginfo  $orderpostinginfo
     */
    public function saving(Orderpostinginfo $orderpostinginfo)
    {
        $orderpostinginfo->user_id = auth()->id();
    }
}
