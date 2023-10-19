<?php

namespace App\Listeners;

use App\Events\FavoriteEvent;
use App\Models\Content;
use App\Models\Contentset;
use App\Models\Contentset;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class FavoriteEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FavoriteEvent  $event
     *
     * @return void
     */
    public function handle(FavoriteEvent $event)
    {
        if ($event instanceof Content) {
            Cache::tags('user_'.$event->user->id.'_favoriteContents')->flush();
        } else {
            if ($event instanceof Contentset) {
                Cache::tags('user_'.$event->user->id.'_favoriteSets')->flush();
            } else {
                if ($event instanceof Product) {
                    Cache::tags('user_'.$event->user->id.'_favoriteProducts')->flush();
                } else {
                    Cache::tags('user_'.$event->user->id.'_favorites')->flush();
                }
            }
        }
        Cache::tags('favorite_'.$event->favorable->id)->flush();
    }
}
