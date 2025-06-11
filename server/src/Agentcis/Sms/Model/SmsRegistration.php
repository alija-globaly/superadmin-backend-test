<?php

namespace Agentcis\Sms\Model;

use Illuminate\Database\Eloquent\Model;

class SmsRegistration extends Model
{
    protected $connection = 'agentcis';

    protected $table= 'sms_registration_requests';

    protected $casts = [
        'user_details' => 'array',
        'address' => 'array'
    ];
}