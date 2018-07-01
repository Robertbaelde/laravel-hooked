<?php

namespace Robertbaelde\Hooked\Events;

use GuzzleHttp\Psr7\Response;
use Illuminate\Queue\SerializesModels;
use Robertbaelde\Hooked\Interfaces\WebhookEventInterface;

class WebhookSuccessfull
{
    use SerializesModels;

    public $event;
    public $webhook;
    public $response;

    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @return void
     */
    public function __construct(WebhookEventInterface $event, array $webhook, Response $response)
    {
        $this->event = $event;
        $this->webhook = $webhook;
        $this->response = $response;
    }
}