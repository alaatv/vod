<?php

namespace App\Providers;

use App\Libraries\Sha1Hasher;
use Illuminate\Support\ServiceProvider;

class Sha1HashServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('hash', function () {
            return new Sha1Hasher();
        });
    }
}
