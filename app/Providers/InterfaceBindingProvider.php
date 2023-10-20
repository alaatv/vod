<?php

namespace App\Providers;

use App\Classes\Format\BlockCollectionFormatter;
use App\Classes\Format\SetCollectionFormatter;
use App\Classes\Format\webBlockCollectionFormatter;
use App\Classes\Format\webSetCollectionFormatter;
use Illuminate\Support\ServiceProvider;

class InterfaceBindingProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(BlockCollectionFormatter::class, webBlockCollectionFormatter::class);
        $this->app->bind(SetCollectionFormatter::class, webSetCollectionFormatter::class);
    }
}
