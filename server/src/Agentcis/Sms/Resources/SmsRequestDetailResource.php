<?php

namespace Agentcis\Sms\Resources;

use Agentcis\Tenant\Model\Tenant;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class SmsRequestDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        $tenant = Tenant::query()->where('id', '=', $this->tenant_id)->first();
        $creditAmount = ($this->credit * 1.20) + ($this->credit * 1.20) * 13 / 100;

        $attachments = json_decode($this->attachments, true);

        $invoices = collect($attachments)->filter(function ($invoice) {
            if (data_get($invoice, 'meta.is_invoice')) {
                return $invoice;
            }
        })->toArray();

        $documents = collect($attachments)->filter(function ($document) {
            if (data_get($document, 'meta.supporting_document_type')) {
                return $document;
            }
        })->toArray();

        return [
            'id' => $this->getKey(),
            'business_details' => [
                'name' => $this->business_name,
                'email' => $this->email
            ],
            'user_details' => $this->user_details,
            'status' => $this->status,
            'applied_on' => Carbon::parse($this->created_at)
                ->timezone('Asia/Kathmandu')
                ->format('Y-m-d H:i:s'),
            'company_domain' => sprintf("https://%s.agentcisapp.com", $tenant->subdomain),
            'subdomain' => $tenant->subdomain,
            'attachments' => $documents,
            'credit' => $this->credit,
            'credit_amount' => $creditAmount,
            'vat_no' => $this->vat_no,
            'phone_number' => sprintf("+977%s", $this->phone_number),
            'address' => $this->address,
            'invoices' => $invoices,
            'rejected_reason' => $this->failure_reason,
            'api_key' => $this->api_key,
            'phone_number_code' => $this->phone_number_code,
            'rejected_on' => $this->when(strtolower($this->status) === 'rejected',
                fn() =>  Carbon::parse($this->updated_at)
                    ->timezone('Asia/Kathmandu')->format('Y-m-d H:i:s'), null)
        ];
    }
}
