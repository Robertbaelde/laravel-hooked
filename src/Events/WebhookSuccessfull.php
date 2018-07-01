<?php

namespace Robertbaelde\Hooked\Events;

use GuzzleHttp\Psr7\Response;
use Illuminate\Queue\SerializesModels;
use Robertbaelde\Hooked\Models\Webhook;

class WebhookSuccessfull
{
    use SerializesModels;

    public $webhook;
    public $response;

    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @return void
     */
    public function __construct(Webhook $webhook, Response $response)
    {
        $this->webhook = $webhook;
        $this->response = $response;
    }
}