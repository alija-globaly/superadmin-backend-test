<?php

namespace Agentcis\WebhookEvent\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $table = 'webhook_notifications';

    protected $casts = ['payload' => 'collection'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'event_name',
        'payload',
        'status',
    ];

    public function history()
    {
        return $this->hasMany(EventHistory::class, 'webhook_notification_id');
    }
}
