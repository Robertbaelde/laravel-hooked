<?php

namespace Robertbaelde\Hooked\Jobs;

// use App\Webhook;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Robertbaelde\Hooked\Events\WebhookFailed;
use Robertbaelde\Hooked\Events\WebhookSuccessfull;
use Robertbaelde\Hooked\Models\Webhook;

class FireWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $webhook;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Webhook $webhook)
    {
        $this->webhook = $webhook;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Client $client)
    {
        // $event = $this->event;
        $webhook = $this->webhook;

        try{
            $response = $client->request($webhook->method, $webhook->url, [
                'json' => [
                    'data' => $webhook->payload
                ]
            ]);
            $webhook->logResponse($response);
        }
        catch (ServerException $e){
            event(new WebhookFailed($webhook, $e));
            // $this->nextInRetrySchema();
        }
    }
}
