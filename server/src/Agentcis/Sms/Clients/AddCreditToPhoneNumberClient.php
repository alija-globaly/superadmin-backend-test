<?php

namespace Agentcis\Sms\Clients;

use Agentcis\Core\AbstractResource;

class AddCreditToPhoneNumberClient extends AbstractResource
{
    public function addCredit(array $details)
    {
        $uri = "/api/v2/sms/nepal/addcredit/webhook";

        return $this->create($uri, $details);
    }
}
