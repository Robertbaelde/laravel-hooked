<?php

namespace Robertbaelde\Hooked\Events;

use Illuminate\Queue\SerializesModels;
use Robertbaelde\Hooked\Models\WebhookCall;

class WebhookSuccessfull
{
    use SerializesModels;

    public $webhookcall;

    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @return void
     */
    public function __construct(WebhookCall $webhookcall)
    {
        $this->webhookcall = $webhookcall;
    }
}