<?php

namespace Agentcis\Sms\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmsRegistrationStatusChangeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['approved', 'rejected', 'updated', 'reverted', 'discontinued'])],
            'rejected_reason' => ['nullable', 'required_if:type,rejected,discontinued', 'string'],
            'credit' => ['nullable', 'required_if:type,approved,updated', 'numeric'],
            'api_key' => ['nullable', 'required_if:type,approved,updated', 'string'],
            'attachments' => ['nullable', 'required_if:type,approved', 'array'],
            'attachments.*' => ['nullable', 'required_if:type,approved', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:5120'],
            'registration_form_id' => ['bail', 'required', 'exists:agentcis.sms_registration_requests,id'],
            'phone_number_code' => ['nullable', 'required_if:type,approved,updated',
                'string',
                Rule::unique('agentcis.sms_registration_requests', 'phone_number_code')
                    ->ignore($this->get('registration_form_id'))
            ],
            'current_user_name' => ['nullable', 'required_if:type,approved,updated', 'string']
        ];
    }
}
