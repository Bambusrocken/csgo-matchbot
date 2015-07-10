<?php

namespace Bot\Providers;

use Log;

use Bot\MatchManagement\UDPSocket;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('Bot\MatchManager\UDPSocket', function(Application $app) {
            return new UDPSocket();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
