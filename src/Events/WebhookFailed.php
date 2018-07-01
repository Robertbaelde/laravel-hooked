<?php

namespace Robertbaelde\Hooked\Events;

use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Queue\SerializesModels;
use Robertbaelde\Hooked\Models\Webhook;

class WebhookFailed
{
    use SerializesModels;

    public $webhook;
    public $e;

    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @return void
     */
    public function __construct(Webhook $webhook, ServerException $e)
    {
        $this->webhook = $webhook;
        $this->e = $e;
    }
}