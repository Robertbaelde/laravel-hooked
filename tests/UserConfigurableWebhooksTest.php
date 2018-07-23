<?php
namespace Robertbaelde\Hooked\Tests;

use Illuminate\Support\Facades\Queue;
use Robertbaelde\Hooked\Jobs\FireWebhook;
use Robertbaelde\Hooked\Models\Webhook;
use Robertbaelde\Hooked\Tests\Events\CustomWebhookEvent;
use Robertbaelde\Hooked\Tests\Models\User;

class UserConfigurableWebhooksTest extends TestCase
{
	public function setUp()
    {
        parent::setUp();
    }
    /** @test */
    function a_event_that_implements_the_configurable_webhook_interface_fires_a_webhook()
    {
        Queue::fake();
        $event = new CustomWebhookEvent();
        event($event);

        $webhookModel = Webhook::first();
        $this->assertNotNull($webhookModel);
        $this->assertEquals('POST', $webhookModel->method);
        $this->assertEquals('https://custom-test.dev', $webhookModel->url);
        $this->assertEquals('custom test webhook', $webhookModel->name);
        $this->assertEquals('Robertbaelde\Hooked\Tests\Events\CustomWebhookEvent', $webhookModel->event);
        $this->assertEquals(['bar' => true], $webhookModel->payload);
        $this->assertTrue($webhookModel->ownerable->is(User::first()));
        
        Queue::assertPushed(FireWebhook::class, function ($job) use ($webhookModel) {
            return $job->webhook->is($webhookModel);
        });
    }
}