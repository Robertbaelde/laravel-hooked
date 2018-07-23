<?php 
return [
	'default_webhooks' => [
		// [
			// 'event' => App\Events\TestEvent::class,
			// 'url' => 'https://requestbin.io/test',
			// 'method' => 'POST',
			// 'name' => "test webhook"
		// ]
	],
	'default_retry_shedule' => [
		10, // 10 seconds
		30, // 30 seconds
		10*60, // 10 mins
		60*60, // 1 hour
		60*60*24, // 1 day
	]
];