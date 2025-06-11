<?php

namespace Agentcis\WebhookEvent\Model;

use Illuminate\Database\Eloquent\Model;

class EventHistory extends Model
{
    protected $table = 'webhook_notification_history';
    
    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'response' => 'collection'
    ];
}
