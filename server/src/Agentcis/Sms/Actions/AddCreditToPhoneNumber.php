<?php

namespace Agentcis\Sms\Actions;

use Agentcis\AgentcisClient;
use Agentcis\Tenant\Model\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AddCreditToPhoneNumber
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'phone_number_code' => ['required', 'string'],
            'credit' => ['required', 'numeric'],
            'credit_added_by' => ['required', 'string'],
            'tenant_id' => ['required', 'exists:agentcis.tenants,id']
        ]);

        $details = $request->only(['phone_number_code', 'credit', 'credit_added_by', 'tenant_id']);

        $tenant = Tenant::query()->find($details['tenant_id']);
        $url = sprintf("https://%s.%s", $tenant->subdomain, env('AGENTCISAPP_DOMAIN'));

        $agentcisClient = new AgentcisClient($url);

        $response = $agentcisClient->addCreditToPhoneNumber()->addCredit($details);

        return new JsonResponse($response);
    }
}
