<?php

namespace Agentcis\Sms\Queries;

use Agentcis\Sms\Model\SmsRegistration;
use Agentcis\Sms\Statuses;

class ListQuery
{
    public function run(string $status = Statuses::STATUS_PENDING, int $perPage = 10)
    {
        return SmsRegistration::query()
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('status', '=', $status);
            })
            ->select([
                'sms_registration_requests.id',
                'sms_registration_requests.business_name',
                'sms_registration_requests.email',
                'sms_registration_requests.status',
                'sms_registration_requests.user_details',
                'sms_registration_requests.created_at',
                'sms_registration_requests.tenant_id',
                'sms_registration_requests.updated_at',
            ])
            ->orderByDesc('sms_registration_requests.created_at')
            ->paginate($perPage);
    }
}