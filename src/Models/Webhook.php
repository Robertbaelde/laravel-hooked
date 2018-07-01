<?php

namespace  Robertbaelde\Hooked\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Robertbaelde\Hooked\Interfaces\WebhookEventInterface;
use Robertbaelde\Hooked\Jobs\FireWebhook;

class Webhook extends Model
{
    public $guarded = [];

    public $timestamps = true;

    public $casts = [
        'payload' => 'array',
    ];
    // 
    public static function fire(WebhookEventInterface $event, array $webhook)
    {
        $self = Self::create([
            'url' => $webhook['url'],
            'method' => $webhook['method'],
            'name' => $webhook['name'],
            'event' => get_class($event),
            'payload' => $event->webhookPayload()
        ]);
        FireWebhook::dispatch($self);

    }

}
