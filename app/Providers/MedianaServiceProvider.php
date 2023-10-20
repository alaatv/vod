<?php

namespace App\Providers;

use App\Broadcasting\MedianaChannel;
use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaClient;
use App\Classes\sms\MedianaPatternClient;
use App\Classes\sms\SmsSenderClient;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\ServiceProvider;

class MedianaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }

    public function register()
    {
        $this->app->when(MedianaChannel::class)
            ->needs(SmsSenderClient::class)
            ->give(function () {
                $config = $this->app['config']['services.medianaSMS.normal'];

                return new MedianaClient(new HttpClient(), $config['userName'], $config['password'], $config['from'],
                    $config['from1000'], $config['url']);
            });

        $this->app->when(MedianaPatternChannel::class)
            ->needs(SmsSenderClient::class)
            ->give(function () {
                return new MedianaPatternClient();
            });
    }
}
