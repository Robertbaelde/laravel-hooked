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
use Illuminate\Support\Facades\Queue;
use Robertbaelde\Hooked\Events\WebhookFailed;
use Robertbaelde\Hooked\Events\WebhookSuccessfull;
use Robertbaelde\Hooked\Jobs\FireWebhook;
use Robertbaelde\Hooked\Models\Webhook;
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
		    new Response(200, [], "{'foo': 'bar'}"),
		]);
		$handler = HandlerStack::create($mock);

		// create history container
		$container = [];
		$history = Middleware::history($container);
		$handler->push($history);
		// replace guzzle client
		$client = new Client(['handler' => $handler]);
		$this->app->instance(\GuzzleHttp\Client::class, $client);
			
		$webhookModel = Webhook::create([
			'url' => 'http://foo.dev/',
			'method' => 'POST',
			'name' => 'test webhook',
			'event' => 'Robertbaelde\Hooked\Tests\Events\TestDefaultEvent',
			'payload' => ['foo' => true],
		]);

		$event = new TestDefaultEvent;
		FireWebhook::dispatch($webhookModel);

	    $this->assertCount(1, $container);

	    $request = $container[0];
	    $this->assertEquals('POST', $request['request']->getMethod());
	    // assert post data is correct
	    $this->assertEquals(['data' => ['foo' => true]], json_decode($request['request']->getBody()->getContents(), true));
	    $this->assertEquals(200, $request['response']->getStatusCode());

	    $this->assertEquals(1, $webhookModel->calls->count());
	    $call = $webhookModel->calls->first();
	    $this->assertEquals(true, $call->successfull);
	    $this->assertEquals(200, $call->response_code);
	    $this->assertEquals("{'foo': 'bar'}", $call->response_body);

	    Event::assertDispatched(WebhookSuccessfull::class, function ($e) use ($call) {
            return $e->webhookcall->is($call);
        });


	}

	/** @test */
	function it_creates_a_new_job_when_the_webhook_failes()
	{
	   	Queue::fake();
		Event::fake();
		// create guzzle mock
		$mock = new MockHandler([
		    new Response(500, []),
		]);
		$handler = HandlerStack::create($mock);
		// replace guzzle client
		$client = new Client(['handler' => $handler]);
		$this->app->instance(\GuzzleHttp\Client::class, $client);

		$webhookModel = Webhook::create([
			'url' => 'http://foo.dev/',
			'method' => 'POST',
			'name' => 'test webhook',
			'event' => 'Robertbaelde\Hooked\Tests\Events\TestDefaultEvent',
			'payload' => ['foo' => true],
		]);

		$job = new FireWebhook($webhookModel);
	    $job->handle($client);
	    
		Event::assertNotDispatched(WebhookSuccessfull::class);
	   	Event::assertDispatched(WebhookFailed::class, function ($e) use ($webhookModel) {
            return $e->webhook->is($webhookModel);
        });
		

	}



}