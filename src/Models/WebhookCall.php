<?php

namespace  Robertbaelde\Hooked\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Robertbaelde\Hooked\Events\WebhookFailed;
use Robertbaelde\Hooked\Events\WebhookSuccessfull;
use Robertbaelde\Hooked\Interfaces\WebhookEventInterface;
use Robertbaelde\Hooked\Jobs\FireWebhook;

class WebhookCall extends Model
{
    public $guarded = [];

    public $timestamps = true;

    public $casts = [
        // 'payload' => 'array',
    ];

    public function webhook()
    {
        return $this->belongsTo(Webhook::class);
    }
    public function getSuccessfullAttribute()
    {
        return $this->response_code < 400;
    }
    public function fireEvent()
    {
        if($this->successfull){
            return event(new WebhookSuccessfull($this));
        }
        return event(new WebhookFailed($this));
    }
}
