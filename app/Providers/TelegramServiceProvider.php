<?php

namespace App\Providers;

use App\Telegram\Telegram;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('telegram', function ($app){
            return new Telegram(new Http());
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
