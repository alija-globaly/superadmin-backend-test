<?php

namespace Agentcis\Sms\Actions;

use Agentcis\Sms\Model\SmsCreditLog;
use Agentcis\Sms\Model\SmsRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ListPhoneNumbers
{
    public function __invoke(Request  $request)
    {
        $request->validate([
            'tenant_id' => ['required']
        ]);
        $tenantId = $request->get('tenant_id');

        $phoneNumbers = SmsCreditLog::query()
            ->where('tenant_id', '=', $tenantId)
            ->distinct()
            ->select(['phone_number_code'])
            ->get();

        return new class ($phoneNumbers) extends ResourceCollection {
            public function toArray($request): array
            {
                return [
                    'phone_number_codes' => $this->map(fn($item) => $item->phone_number_code)
                ];
            }
        };
    }
}