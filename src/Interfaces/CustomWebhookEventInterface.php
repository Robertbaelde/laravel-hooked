<?php 
namespace Robertbaelde\Hooked\Interfaces;

interface CustomWebhookEventInterface
{
	public function webhookPayload();
	public function getWebhookUrl();
	public function getWebhookMethod();
	public function getWebhookName();
	// public function webhookOwner();
}