<?php
namespace Robertbaelde\Hooked\Tests\Jobs;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Event;
use Robertbaelde\Hooked\Events\WebhookSuccessfull;
use Robertbaelde\Hooked\Jobs\FireWebhook;
use Robertbaelde\Hooked\Tests\Events\TestDefaultEvent;
use Robertbaelde\Hooked\Tests\TestCase;

class FireWebhookTest extends TestCase
{

	/** @test */
	function a_fire_webhook_job_makes_a_request_to_the_endpoint()
	{
		Event::fake();

		// create guzzle mock
		$mock = new MockHandler([
		    new Response(200, []),
		]);
		$handler = HandlerStack::create($mock);

		// create history container
		$container = [];
		$history = Middleware::history($container);
		$handler->push($history);
		// replace guzzle client
		$client = new Client(['handler' => $handler]);
		$this->app->instance(\GuzzleHttp\Client::class, $client);
		$event = new TestDefaultEvent;
		FireWebhook::dispatch($event, [
			'event' => TestDefaultEvent::class,
            'url' => 'http://foo.dev/',
            'method' => 'POST',
            'name' => "test webhook"
	    ]);

	    $this->assertCount(1, $container);

	    $request = $container[0];
	    $this->assertEquals('POST', $request['request']->getMethod());
	    // assert post data is correct
	    $this->assertEquals(['data' => ['foo' => true]], json_decode($request['request']->getBody()->getContents(), true));
	    $this->assertEquals(200, $request['response']->getStatusCode());

	    Event::assertDispatched(WebhookSuccessfull::class, function ($e) use ($event) {
            return $e->event == $event;
        });
	}



}