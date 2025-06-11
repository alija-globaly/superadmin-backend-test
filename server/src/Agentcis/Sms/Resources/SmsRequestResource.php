<?php

namespace Agentcis\Sms\Resources;

use Agentcis\Tenant\Model\Tenant;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class SmsRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        $tenant = Tenant::query()->where('id', '=', $this->tenant_id)->first();
        $appliedOn = Carbon::parse($this->created_at)->timezone('Asia/Kathmandu');
        $rejectedOn = Carbon::parse($this->updated_at)->timezone('Asia/Kathmandu');
        return [
            'id' => $this->getKey(),
            'business_details' => [
                'name' => $this->business_name,
                'email' => $this->email
            ],
            'user_details' => $this->user_details,
            'status' => $this->status,
            'applied_on' => $appliedOn->format('Y-m-d H:i:s'),
            'company_domain' => sprintf("%s.agentcisapp.com", $tenant->subdomain),
            'rejected_on' => $this->when(strtolower($this->status) === 'rejected',
                fn() => $rejectedOn->format('Y-m-d H:i:s'), null)
        ];
    }
}