<?php

namespace Agentcis\Sms\Actions;

use Agentcis\Sms\Queries\ListCreditLogQuery;
use Agentcis\Sms\Resources\SmsCreditLogResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListSmsCreditLogs
{
    public function __invoke(Request  $request, ListCreditLogQuery $creditLogQuery): AnonymousResourceCollection
    {
        $request->validate([
            'phone_number_code' => ['required'],
            'tenant_id' => ['required']
        ]);

        $phoneNumberCode = $request->get('phone_number_code');
        $tenant = $request->get('tenant_id');

        $logs = $creditLogQuery->run($phoneNumberCode, $tenant);
        return SmsCreditLogResource::collection($logs);
    }
}