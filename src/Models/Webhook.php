<?php

namespace  Robertbaelde\Hooked\Models;

use GuzzleHttp\Psr7\Response;
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

    public function calls()
    {
        return $this->hasMany(WebhookCall::class);
    }
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
    public function logResponse(Response $response, $start_time)
    {
        return $this->calls()->create([
            'response_code' => $response->getStatusCode(),
            'response_body' => $response->getBody()->getContents(),
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
