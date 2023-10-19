<?php

namespace App\Providers;

use App\Classes\UserFavored;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class UserFavoredProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('App\Classes\UserFavored', function () {
            $request = $this->app->make(Request::class);
            $user = $request->user();
            return new UserFavored($user, $request->query('type'));
        });
    }
}
