<?php

namespace Agentcis;

use Agentcis\Core\TenantConfigChangerClient;
use Agentcis\Sms\Clients\AddCreditToPhoneNumberClient;
use Agentcis\Sms\Clients\SmsRegistrationFormClient;
use GuzzleHttp\Client as HttpClient;

class AgentcisClient
{
    public $agentcisClient;

    public function __construct(string $url)
    {
        $this->agentcisClient = new HttpClient([
            'headers' => [
                'Content-type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'base_uri' => $url,
        ]);
    }

    public function smsRegistrationForm()
    {
        return new SmsRegistrationFormClient($this->agentcisClient);
    }

    public function addCreditToPhoneNumber()
    {
        return new AddCreditToPhoneNumberClient($this->agentcisClient);
    }

    public function tenantConfig()
    {
        return new TenantConfigChangerClient($this->agentcisClient);
    }
}
