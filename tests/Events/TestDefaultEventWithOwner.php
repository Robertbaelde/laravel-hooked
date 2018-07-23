<?php

namespace Robertbaelde\Hooked\Tests\Events;

use Illuminate\Queue\SerializesModels;
use Robertbaelde\Hooked\Interfaces\WebhookEventInterface;
use Robertbaelde\Hooked\Tests\Models\Account;


class TestDefaultEventWithOwner implements WebhookEventInterface
{
    use SerializesModels;

    public $owner;
    public function __construct($owner)
    {
    	$this->owner = $owner;
    }

    public function webhookPayload()
    {
        return ['foo' => true];
    }

    public function webhookOwner()
	{
	    return $this->owner;
	}
}