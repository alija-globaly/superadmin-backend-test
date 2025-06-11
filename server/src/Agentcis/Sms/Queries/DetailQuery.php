<?php

namespace Agentcis\Sms\Queries;

use Agentcis\Sms\Model\SmsRegistration;

class DetailQuery
{
    public function run(int $registrationId)
    {
        return SmsRegistration::query()
            ->where('sms_registration_requests.id', '=', $registrationId)
            ->first();
    }
}