<?php

namespace Robertbaelde\Hooked\Jobs;

// use App\Webhook;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Robertbaelde\Hooked\Interfaces\WebhookEventInterface;
// use Robertbaelde\Hooked\Jobs\Interfaces\WebhookEventInterface;

class FireWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $event;
    public $webhook;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(WebhookEventInterface $event, array $webhook)
    {
        $this->event = $event;
        $this->webhook = $webhook;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Client $client)
    {
        $event = $this->event;
        $webhook = $this->webhook;

        try{
            $response = $client->request($webhook['method'], $webhook['url'], [
                'json' => [
                    'data' => $event->webhookPayload()
                ]
            ]);
            // dd($response->getBody()->getContents());
        }
        catch (Exception $e){
            // log failure
            throw $e;
        }
    }
}
