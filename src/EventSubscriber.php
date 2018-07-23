<?php

namespace Robertbaelde\Hooked;

use Robertbaelde\Hooked\Interfaces\CustomWebhookEventInterface;
use Robertbaelde\Hooked\Interfaces\WebhookEventInterface;
use Robertbaelde\Hooked\Jobs\FireWebhook;
use Robertbaelde\Hooked\Models\Webhook;

class EventSubscriber
{
    
    public function handleEvent(string $eventName, $payload = null)
    {
        if(($event = head($payload)) instanceof WebhookEventInterface){
            $this->handleWebhook($event);
        }
        if(($event = head($payload)) instanceof CustomWebhookEventInterface){
            $this->handleCustomWebhook($event);
        }
    }

    public function handleWebhook($event)
    {
        collect(config('webhooks.default_webhooks'))->each(function($webhook) use ($event){
            if($event instanceof $webhook['event']){
                Webhook::fire($event, $webhook);
            }
        });
    }

    public function handleCustomWebhook($event)
    {
        Webhook::fireCustom($event);
    }

    public function subscribe($events)
    {
        $events->listen('*', static::class . '@handleEvent');
    }
}