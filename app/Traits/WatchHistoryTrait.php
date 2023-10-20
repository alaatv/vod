<?php

namespace App\Traits;


use App\Models\WatchHistory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait WatchHistoryTrait
{
    /**
     * Get all of the Models' watches.
     *
     * @return MorphMany
     */
    public function watches(): MorphMany
    {
        return $this->morphMany(WatchHistory::class, 'watchable');
    }
}
