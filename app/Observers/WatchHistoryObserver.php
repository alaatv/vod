<?php

namespace App\Observers;

use App\Models\Content;
use App\Models\WatchHistory;
use Illuminate\Support\Facades\Cache;

class WatchHistoryObserver
{
    /**
     * Handle the watch history "created" event.
     *
     * @param  WatchHistory  $watchHistory
     *
     * @return void
     */
    public function created(WatchHistory $watchHistory)
    {
        //
    }

    /**
     * Handle the watch history "updated" event.
     *
     * @param  WatchHistory  $watchHistory
     *
     * @return void
     */
    public function updated(WatchHistory $watchHistory)
    {
        //
    }

    /**
     * Handle the watch history "deleted" event.
     *
     * @param  WatchHistory  $watchHistory
     *
     * @return void
     */
    public function deleted(WatchHistory $watchHistory)
    {
        //
    }

    /**
     * Handle the watch history "restored" event.
     *
     * @param  WatchHistory  $watchHistory
     *
     * @return void
     */
    public function restored(WatchHistory $watchHistory)
    {
        //
    }

    /**
     * Handle the watch history "force deleted" event.
     *
     * @param  WatchHistory  $watchHistory
     *
     * @return void
     */
    public function forceDeleted(WatchHistory $watchHistory)
    {
        //
    }

    /**
     * @param  WatchHistory  $watchHistory
     *
     * @return void
     */
    public function saving(WatchHistory $watchHistory)
    {
        $watchHistory->user_id = auth()->id();
    }

    /**
     * @param  WatchHistory  $watchHistory
     *
     * @return void
     */
    public function saved(WatchHistory $watchHistory)
    {
        $userId = auth()->id();
        $tagPrefix = "user_{$userId}_";

        if ($watchHistory->watchable_type != config('constants.MORPH_MAP_MODELS.content.model')) {
            return;
        }
        /** @var Content $content */
        $content = $watchHistory->watchable;
        $products = optional($content->set)->products;
        foreach ($products as $product) {
            Cache::tags([$tagPrefix."product_{$product->id}_nextWatchContent"])->flush();
        }

    }
}
