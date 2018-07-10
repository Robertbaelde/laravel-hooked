<?php 
return [
	'default_webhooks' => [
		[
			// 'event' => App\Events\TestEvent::class,
			// 'url' => 'https://requestbin.io/test',
			// 'method' => 'POST',
			// 'name' => "test webhook"
		]
	],
	'user_configureable_webhooks' => [
		// "name" => "test configurable webhook",
	],
	
	'default_retry_shedule' => [
		[
		'interval' => 10, // seconds
		'times' => 2,
		],
		[
		'interval' => 10*60, // seconds
		'times' => 1,
		],
		[
		'interval' => 60*60, // seconds
		'times' => 1,
		],
		[
		'interval' => 60*60*24, // seconds
		'times' => 3,
		]
	]
];