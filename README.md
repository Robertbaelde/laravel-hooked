# Comprehensive webhooks for Laravel ➡️

Webhooks are used to let applications push data to other third-party services.

This package enables to send webhooks when a event is fired. Simple webhooks can be configured in the configuration, while more dynamic webhooks can be created by adding a few extra methods to the event.

## Installation

## Installation in 4 steps

### Step 1: Install package

Add the package in your composer.json by executing the command.

```bash
composer require robertbaelde/hooked
```

The package will automatically register its service provider

### Step 2: Migrations

Run the followning command to publish the database migrations for this package.

```bash
php artisan vendor:publish --provider="Robertbaelde\Hooked\HookedServiceProvider" --tag="migrations"
```

Run `php artisan:migrate` to run the migration fliles.

### Step 4: Configuration

Run the followning command to publish the configuration file for this package.

```bash
php artisan vendor:publish --provider="Robertbaelde\Hooked\HookedServiceProvider" --tag="config"
```

This is the default content of the config file that will be published at config/webhooks.php:

```php
return [
	'default_webhooks' => [
		// [
			// 'event' => App\Events\TestEvent::class,
			// 'url' => 'https://requestbin.io/test',
			// 'method' => 'POST',
			// 'name' => "Test webhook"
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
```

## Default webhooks

When a webhook needs to be fired with the same url everytime a event is fired, you can use default webhooks. Those are configured in the webhooks.php config file. Simply specify the event that it belongs to, the url it needs to call, the http method to be used and the name for the webhook.

```php
return [
	'default_webhooks' => [
		[
			'event' => App\Events\TestEvent::class,
			'url' => 'https://requestbin.io/test',
			'method' => 'POST',
			'name' => "Test webhook"
		]
	]
];
```

Furthermore the event need to implement the `WebhookEventInterface`, this requires the class to have a webhookPayload function. The data returned from this function will be included in the webhook payload data.

```php
<?php
namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Robertbaelde\Hooked\Interfaces\WebhookEventInterface;

class TestDefaultEvent implements WebhookEventInterface
{
    use SerializesModels;

    public function webhookPayload()
    {
        return ['foo' => true];
    }
}
```

## Security

If you discover any security related issues, please email robert@baelde.nl instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
