<?php

namespace Robertbaelde\Hooked\Events;

use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Queue\SerializesModels;
use Robertbaelde\Hooked\Models\Webhook;
use Robertbaelde\Hooked\Models\WebhookCall;

class WebhookFailed
{
    use SerializesModels;

    public $webhook_call;

    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @return void
     */
    public function __construct(WebhookCall $webhook_call)
    {
        $this->webhook_call = $webhook_call;
    }
}