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
            if (! class_exists('CreateWebhooksTable')) {
                $this->publishes([
                    __DIR__.'/../stubs/create_webhooks_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_webhooks_table.php'),
                ], 'migrations');
            }

        // if (! class_exists('CreateProjectorStatusesTable')) {
        //     $this->publishes([
        //         __DIR__.'/../stubs/create_projector_statuses_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_projector_statuses_table.php'),
        //     ], 'migrations');
        // }
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
