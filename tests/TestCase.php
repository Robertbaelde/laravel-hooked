<?php

namespace Robertbaelde\Hooked\Tests;

use File;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Database\Eloquent\Relations\Relation;
use Robertbaelde\Hooked\HookedServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            HookedServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
        Schema::dropIfExists('webhooks');
        include_once __DIR__.'/../stubs/create_webhooks_table.php.stub';
        (new \CreateWebhooksTable())->up();

        Schema::dropIfExists('webhook_calls');
        include_once __DIR__.'/../stubs/create_webhook_calls_table.php.stub';
        (new \CreateWebhookCallsTable())->up();
    }
}
