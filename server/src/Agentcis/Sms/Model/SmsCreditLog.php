<?php

namespace Agentcis\Sms\Model;

use Illuminate\Database\Eloquent\Model;

class SmsCreditLog extends Model
{
    protected $table = 'sms_credit_logs';
    protected $connection = 'agentcis';

    public const STATUS_REQUESTED = 'requested';
    public const STATUS_APPROVED = 'approved';
}