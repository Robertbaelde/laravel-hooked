<?php

namespace  Robertbaelde\Hooked\Models;

use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Robertbaelde\Hooked\Interfaces\CustomWebhookEventInterface;
use Robertbaelde\Hooked\Interfaces\WebhookEventInterface;
use Robertbaelde\Hooked\Jobs\FireWebhook;

class Webhook extends Model
{
    public $guarded = [];

    public $timestamps = true;

    public $casts = [
        'payload' => 'array',
    ];

    public function calls()
    {
        return $this->hasMany(WebhookCall::class);
    }

    public function ownerable()
    {
        return $this->morphTo();
    }
    // 
    public static function fire(WebhookEventInterface $event, array $webhook)
    {
        $self = Self::make([
            'url' => $webhook['url'],
            'method' => $webhook['method'],
            'name' => $webhook['name'],
            'event' => get_class($event),
            'payload' => $event->webhookPayload()
        ]);
        if(method_exists($event, 'webhookOwner')){
            $self->ownerable()->associate($event->webhookOwner());
        }
        $self->save();
        FireWebhook::dispatch($self);
    }
    public static function fireCustom(CustomWebhookEventInterface $event)
    {
        $self = Self::make([
            'url' => $event->getWebhookUrl(),
            'method' => $event->getWebhookMethod(),
            'name' => $event->getWebhookName(),
            'event' => get_class($event),
            'payload' => $event->webhookPayload()
        ]);
        if(method_exists($event, 'webhookOwner')){
            $self->ownerable()->associate($event->webhookOwner());
        }
        $self->save();
        FireWebhook::dispatch($self);
    }
    public function logResponse(Response $response, $start_time)
    {
        return $this->calls()->create([
            'response_code' => $response->getStatusCode(),
            'response_body' => $response->getBody()->getContents(),
            'duration' => (microtime(true)-$start_time)
        ])->fireEvent();
    }
    public function logError($message, $start_time)
    {
        return $this->calls()->create([
            'response_code' => 0,
            'response_body' => $message,
            'duration' => microtime(true)-$start_time
        ])->fireEvent();
    }

    public function getNextFireTime()
    {
        $tries = $this->calls->count();
        if($tries == 0){
            return now();
        }
        $retry_shedule = collect(config('webhooks.default_retry_shedule'));
        return now()->addSeconds($retry_shedule->take($tries)->sum());
    }

}
