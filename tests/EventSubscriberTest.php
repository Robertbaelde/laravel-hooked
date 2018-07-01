<?php

namespace Robertbaelde\Hooked\Tests;

use Illuminate\Support\Facades\Queue;
use Robertbaelde\Hooked\Jobs\FireWebhook;
use Robertbaelde\Hooked\Models\Webhook;
use Robertbaelde\Hooked\Tests\Events\TestDefaultEvent;

class EventSubscriberTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_wil_fire_a_webhook_when_a_event_is_configured_and_extends_the_webhook_interface()
    {
        config(['webhooks.default_webhooks' => [
                [
                    'event' => TestDefaultEvent::class,
                    'url' => 'test.dev',
                    'method' => 'POST',
                    'name' => "test webhook"
                ]
        ]]);

        Queue::fake();
        $event = new TestDefaultEvent();
        event($event);

        $webhookModel = Webhook::first();
        $this->assertNotNull($webhookModel);
        $this->assertEquals('POST', $webhookModel->method);
        $this->assertEquals('test.dev', $webhookModel->url);
        $this->assertEquals('test webhook', $webhookModel->name);
        $this->assertEquals('Robertbaelde\Hooked\Tests\Events\TestDefaultEvent', $webhookModel->event);
        $this->assertEquals(['foo' => true], $webhookModel->payload);
        
        Queue::assertPushed(FireWebhook::class, function ($job) use ($webhookModel) {
            return $job->webhook->is($webhookModel);
        });
    }
}