<?php

namespace Robertbaelde\Hooked;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Robertbaelde\Hooked\EventSubscriber;

class HookedServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/webhooks.php' => config_path('webhooks.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/webhooks.php', 'webhooks');
        Event::subscribe(EventSubscriber::class);
    }
}
