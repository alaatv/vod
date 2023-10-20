<?php

namespace App\Observers;

use App\Models\Source;
use Illuminate\Support\Facades\Cache;

class SourceObserver
{
    /**
     * Handle the source "created" event.
     *
     * @param  Source  $source
     *
     * @return void
     */
    public function created(Source $source)
    {
        //
    }

    /**
     * Handle the source "updated" event.
     *
     * @param  Source  $source
     *
     * @return void
     */
    public function updated(Source $source)
    {
        //
    }

    /**
     * Handle the source "deleted" event.
     *
     * @param  Source  $source
     *
     * @return void
     */
    public function deleted(Source $source)
    {
        //
    }

    /**
     * Handle the source "restored" event.
     *
     * @param  Source  $source
     *
     * @return void
     */
    public function restored(Source $source)
    {
        //
    }

    /**
     * Handle the source "force deleted" event.
     *
     * @param  Source  $source
     *
     * @return void
     */
    public function forceDeleted(Source $source)
    {
        //
    }

    public function saved(Source $source)
    {
        Cache::tags([
            'source_'.$source->id,
        ])->flush();
    }
}
