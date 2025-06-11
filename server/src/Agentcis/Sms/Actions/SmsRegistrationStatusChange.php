<?php

namespace Agentcis\Sms\Actions;

use Agentcis\AgentcisClient;
use Agentcis\Sms\Manager;
use Agentcis\Sms\Requests\SmsRegistrationStatusChangeRequest;
use Agentcis\Tenant\Model\Tenant;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;

class SmsRegistrationStatusChange
{
    public function __invoke(SmsRegistrationStatusChangeRequest $request, Manager $manager)
    {
        $details = $request->validated();
        $registration = $manager->find($details['registration_form_id']);
        $tenant = Tenant::query()->find($registration->tenant_id);
        $url = sprintf("https://%s.%s", $tenant->subdomain, env('AGENTCISAPP_DOMAIN'));

        $agentcisClient = new AgentcisClient($url);

        $response = $agentcisClient->smsRegistrationForm()->updateStatus($details);

        return new JsonResponse($response);
    }
}
