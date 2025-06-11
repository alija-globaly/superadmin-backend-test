<?php

namespace Agentcis\Sms\Actions;

use Agentcis\Sms\Manager;
use Agentcis\Sms\Queries\DetailQuery;
use Agentcis\Sms\Resources\SmsRequestDetailResource;

class SmsRegistrationDetail
{
    public function __invoke(int $registrationId, DetailQuery $detailQuery, Manager  $manager): SmsRequestDetailResource
    {
        $registration = $manager->find($registrationId);

        return new SmsRequestDetailResource($registration);
    }
}