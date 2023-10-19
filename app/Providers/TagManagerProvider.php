<?php

namespace App\Providers;

use App\Classes\Search\Tag\AuthorTagManagerViaApi;
use App\Classes\Search\Tag\ContentsetTagManagerViaApi;
use App\Classes\Search\Tag\ContentTagManagerViaApi;
use App\Classes\Search\Tag\LiveDescriptionTagManagerViaApi;
use App\Classes\Search\Tag\MapDetailTagManagerViaApi;
use App\Classes\Search\Tag\ProductTagManagerViaApi;
use App\Classes\Search\Tag\TaggingInterface;
use App\Classes\Search\Tag\TicketTagManagerViaApi;
use App\Console\Commands\AuthorTagCommand;
use App\Console\Commands\ContentTagCommand;
use App\Console\Commands\ProductTagCommand;
use App\Console\Commands\SetTagCommand;
use App\Observers\ContentObserver;
use App\Observers\LiveDescriptionObserver;
use App\Observers\MapDetailObserver;
use App\Observers\ProductObserver;
use App\Observers\SetObserver;
use App\Observers\TicketObserver;
use Illuminate\Support\ServiceProvider;

class TagManagerProvider extends ServiceProvider
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
    public function register()
    {
        $this->app->when(LiveDescriptionObserver::class)
            ->needs(TaggingInterface::class)
            ->give(function () {
                return (new LiveDescriptionTagManagerViaApi());
            });


        $this->app->when(ContentObserver::class)
            ->needs(TaggingInterface::class)
            ->give(function () {
                return (new ContentTagManagerViaApi());
            });

        $this->app->when(SetObserver::class)
            ->needs(TaggingInterface::class)
            ->give(function () {
                return (new ContentsetTagManagerViaApi());
            });

        $this->app->when(ProductObserver::class)
            ->needs(TaggingInterface::class)
            ->give(function () {
                return (new ProductTagManagerViaApi());
            });

        $this->app->when(ContentTagCommand::class)
            ->needs(TaggingInterface::class)
            ->give(function () {
                return (new ContentTagManagerViaApi());
            });

        $this->app->when(ProductTagCommand::class)
            ->needs(TaggingInterface::class)
            ->give(function () {
                return (new ProductTagManagerViaApi());
            });

        $this->app->when(SetTagCommand::class)
            ->needs(TaggingInterface::class)
            ->give(function () {
                return (new ContentsetTagManagerViaApi());
            });

        $this->app->when(AuthorTagCommand::class)
            ->needs(TaggingInterface::class)
            ->give(function () {
                return (new AuthorTagManagerViaApi());
            });

        $this->app->when(TicketObserver::class)
            ->needs(TaggingInterface::class)
            ->give(function () {
                return (new TicketTagManagerViaApi());
            });

        $this->app->when(MapDetailObserver::class)
            ->needs(TaggingInterface::class)
            ->give(function () {
                return (new MapDetailTagManagerViaApi());
            });
    }
}
