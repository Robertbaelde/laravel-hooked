<?php

namespace Robertbaelde\Hooked\Tests\Events;

use Illuminate\Queue\SerializesModels;
use Robertbaelde\Hooked\Interfaces\WebhookEventInterface;
use Robertbaelde\Hooked\Tests\Models\Account;


class TestDefaultEvent implements WebhookEventInterface
{
    use SerializesModels;

    public function __construct()
    {
    }

    public function webhookPayload()
    {
        return ['foo' => true];
    }
}