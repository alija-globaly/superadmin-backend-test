<?php

namespace Agentcis\Sms\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SmsCreditLogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'credit_requested_by' => json_decode($this->credit_requested_by),
            'credit_amount' => $this->credit_amount,
            'requested_on' => $this->created_at->format('Y-m-d H:i:s'),
            'added_by' => $this->credit_added_by,
            'tenant_id' => $this->tenant_id,
            'phone_number_code' => $this->phone_number_code,
            'added_on' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}