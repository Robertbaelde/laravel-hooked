<?php

namespace Robertbaelde\Hooked\Tests;

use Illuminate\Support\Facades\Queue;
use Robertbaelde\Hooked\Jobs\FireWebhook;
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

        Queue::assertPushed(FireWebhook::class, function ($job) use ($event) {
            $this->assertEquals($job->webhook['url'], 'test.dev');
            $this->assertEquals($job->webhook['method'], 'POST');
            return $job->event == $event;
        });
    }
}