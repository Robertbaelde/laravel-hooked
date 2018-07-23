<?php
namespace Robertbaelde\Hooked\Tests;

use Illuminate\Support\Facades\Queue;
use Robertbaelde\Hooked\Tests\Events\TestDefaultEventWithOwner;
use Robertbaelde\Hooked\Tests\Models\User;
use Robertbaelde\Hooked\Jobs\FireWebhook;
use Robertbaelde\Hooked\Models\Webhook;

class OwnableWebhooksTest extends TestCase
{
   /** @test */
   function a_webhook_can_belong_to_a_user()
   {
       config(['webhooks.default_webhooks' => [
                [
                    'event' => TestDefaultEventWithOwner::class,
                    'url' => 'test.dev',
                    'method' => 'POST',
                    'name' => "test webhook"
                ]
        ]]);

        Queue::fake();
        $user = User::create();
        $event = new TestDefaultEventWithOwner($user);
        event($event);

        $webhookModel = Webhook::first();
        $this->assertNotNull($webhookModel);
        $this->assertEquals('POST', $webhookModel->method);
        $this->assertEquals('test.dev', $webhookModel->url);
        $this->assertEquals('test webhook', $webhookModel->name);
        $this->assertEquals('Robertbaelde\Hooked\Tests\Events\TestDefaultEventWithOwner', $webhookModel->event);
        $this->assertEquals(['foo' => true], $webhookModel->payload);
        $this->assertTrue($webhookModel->ownerable->is($user));
        
        Queue::assertPushed(FireWebhook::class, function ($job) use ($webhookModel) {
            return $job->webhook->is($webhookModel);
        });
   }

   /** @test */
   function it_doesnt_blow_up_when_the_owner_object_is_not_a_eloquent_model_and_is_not_null()
   {
       	config(['webhooks.default_webhooks' => [
            [
                'event' => TestDefaultEventWithOwner::class,
                'url' => 'test.dev',
                'method' => 'POST',
                'name' => "test webhook"
            ]
        ]]);

        Queue::fake();
        $event = new TestDefaultEventWithOwner([]);
        event($event);

        $webhookModel = Webhook::first();
        $this->assertNotNull($webhookModel);
        $this->assertEquals('POST', $webhookModel->method);
        $this->assertEquals('test.dev', $webhookModel->url);
        $this->assertEquals('test webhook', $webhookModel->name);
        $this->assertEquals('Robertbaelde\Hooked\Tests\Events\TestDefaultEventWithOwner', $webhookModel->event);
        $this->assertEquals(['foo' => true], $webhookModel->payload);
        $this->assertEquals(null, $webhookModel->ownerable);
        
        Queue::assertPushed(FireWebhook::class, function ($job) use ($webhookModel) {
            return $job->webhook->is($webhookModel);
        });
   }
}