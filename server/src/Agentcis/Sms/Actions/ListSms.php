<?php

namespace Agentcis\Sms\Actions;

use Agentcis\Sms\Queries\ListQuery;
use Agentcis\Sms\Resources\SmsRequestResource;
use Agentcis\Sms\Statuses;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListSms
{
    public function __invoke(Request  $request, ListQuery  $listQuery): AnonymousResourceCollection
    {
        $perPage = $request->get('per_page', 10);
        $status = $request->get('status', Statuses::STATUS_PENDING);

        $smsRegistrationRequests = $listQuery->run($status, $perPage);

        return SmsRequestResource::collection($smsRegistrationRequests);
    }
}