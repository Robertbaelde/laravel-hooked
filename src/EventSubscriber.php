<?php

namespace Robertbaelde\Hooked;

use Robertbaelde\Hooked\Interfaces\WebhookEventInterface;
use Robertbaelde\Hooked\Jobs\FireWebhook;

class EventSubscriber
{
    public function handleEvent(string $eventName, $payload = null)
    {
        if(!($event = head($payload)) instanceof WebhookEventInterface){
            return;
        }

        collect(config('webhooks.default_webhooks'))->each(function($webhook) use ($event){
            if($event instanceof $webhook['event']){
                FireWebhook::dispatch($event, $webhook);
            }
        });
    }

    public function subscribe($events)
    {
        $events->listen('*', static::class . '@handleEvent');
    }
}