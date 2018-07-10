<?php

namespace Robertbaelde\Hooked\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;
use Robertbaelde\Hooked\Jobs\FireWebhook;
use Robertbaelde\Hooked\Models\Webhook;
use Robertbaelde\Hooked\Tests\Events\TestDefaultEvent;

class RetrySheduleTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

   /** @test */
   function a_default_retry_shedule_can_be_configured()
   {
        Carbon::setTestNow(Carbon::create(2018, 1, 1, 12, 00, 00));   // 2018-01-01 12:00:00
        config(['webhooks.default_retry_shedule' => [60, 120]]);

        $webhookModel = Webhook::create([
            'url' => 'http://foo.dev/',
            'method' => 'POST',
            'name' => 'test webhook',
            'event' => 'Robertbaelde\Hooked\Tests\Events\TestDefaultEvent',
            'payload' => ['foo' => true],
        ]);

        $this->assertEquals('2018-01-01 12:00:00', $webhookModel->getNextFireTime()->toDateTimeString());
        // create failed hook log
        $webhookModel->calls()->create([
            'response_code' => 500,
            'response_body' => '',
            'duration' => 0
        ]);
        $this->assertEquals('2018-01-01 12:01:00', $webhookModel->fresh()->getNextFireTime()->toDateTimeString());

        // create failed hook log
        $webhookModel->calls()->create([
            'response_code' => 500,
            'response_body' => '',
            'duration' => 0
        ]);
        $this->assertEquals('2018-01-01 12:03:00', $webhookModel->fresh()->getNextFireTime()->toDateTimeString());

   }
}