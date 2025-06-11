<?php

namespace Agentcis\Sms\Queries;

use Agentcis\Sms\Model\SmsCreditLog;

class ListCreditLogQuery
{
    public function run(string $phoneNumberCode, int $tenantId)
    {
        return SmsCreditLog::query()
            ->where('status', '=', SmsCreditLog::STATUS_APPROVED)
            ->where('phone_number_code', '=', $phoneNumberCode)
            ->where('tenant_id', '=', $tenantId)
            ->get();
    }
}