<?php

namespace App\Observers;

use App\Models\Ordermanagercomment;

class OrderManagerCommentObserver
{
    /**
     * Handle the ordermanagercomment "created" event.
     *
     * @param  Ordermanagercomment  $ordermanagercomment
     * @return void
     */
    public function created(Ordermanagercomment $ordermanagercomment)
    {
        //
    }

    /**
     * Handle the ordermanagercomment "updated" event.
     *
     * @param  Ordermanagercomment  $ordermanagercomment
     * @return void
     */
    public function updated(Ordermanagercomment $ordermanagercomment)
    {
        //
    }

    /**
     * Handle the ordermanagercomment "deleted" event.
     *
     * @param  Ordermanagercomment  $ordermanagercomment
     * @return void
     */
    public function deleted(Ordermanagercomment $ordermanagercomment)
    {
        //
    }

    /**
     * Handle the ordermanagercomment "restored" event.
     *
     * @param  Ordermanagercomment  $ordermanagercomment
     * @return void
     */
    public function restored(Ordermanagercomment $ordermanagercomment)
    {
        //
    }

    /**
     * Handle the ordermanagercomment "force deleted" event.
     *
     * @param  Ordermanagercomment  $ordermanagercomment
     * @return void
     */
    public function forceDeleted(Ordermanagercomment $ordermanagercomment)
    {
        //
    }

    /**
     * Handle the Ordermanagercomment "creating" and "updating" event.
     *
     * @param  Ordermanagercomment  $ordermanagercomment
     */
    public function saving(Ordermanagercomment $ordermanagercomment)
    {
        $ordermanagercomment->user_id = auth()->id();
    }
}
