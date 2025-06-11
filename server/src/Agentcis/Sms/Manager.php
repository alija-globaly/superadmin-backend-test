<?php

namespace Agentcis\Sms;

use Agentcis\Sms\Model\SmsRegistration;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Manager
{
    /**
     * @var SmsRegistration
     */
    private $registration;

    /**
     * @param SmsRegistration $registration
     */
    public function __construct(SmsRegistration $registration)
    {
        $this->registration = $registration;
    }

    public function find(int $registrationId)
    {
        $registration = $this->registration->newQuery()->where('id', $registrationId)->first();

        throw_if(!$registration, new ModelNotFoundException($registration));

        return $registration;
    }
}