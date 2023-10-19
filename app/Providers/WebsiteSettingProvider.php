<?php

namespace App\Providers;

use App\Models\Websitesetting;
use Illuminate\Support\Facades\{Cache};
use Illuminate\Support\ServiceProvider;

class WebsiteSettingProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Websitesetting::class, function ($app) {
            return $this->getSetting();
        });
    }

    /**
     * @return mixed
     */
    private function getSetting()
    {
        $key = 'AppServiceProvider:websitesettings';

        return Cache::tags(['websiteSetting', 'websiteSetting_version_1'])->remember($key,
            config('constants.CACHE_600'), function () {
                return Websitesetting::where('version', 1)
                    ->first();
            });

    }

    public function provides()
    {
        return [
            Websitesetting::class,
        ];
    }
}
