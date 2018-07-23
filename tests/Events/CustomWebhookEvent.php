<?php

namespace Robertbaelde\Hooked\Tests\Events;

use Illuminate\Queue\SerializesModels;
use Robertbaelde\Hooked\Interfaces\CustomWebhookEventInterface;
use Robertbaelde\Hooked\Tests\Models\Account;
use Robertbaelde\Hooked\Tests\Models\User;


class CustomWebhookEvent implements CustomWebhookEventInterface
{
    use SerializesModels;

    public function __construct()
    {
    }

     public function webhookPayload()
    {
        return ['bar' => true];
    }

	public function getWebhookUrl()
	{
		return 'https://custom-test.dev';
	}
	public function getWebhookMethod()
	{
		return 'POST';
	}
	public function getWebhookName()
	{
		return 'custom test webhook';
	}

	public function webhookOwner()
	{
	    return User::create();
	}

   
}