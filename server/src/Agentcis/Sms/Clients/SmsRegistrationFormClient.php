<?php

namespace Agentcis\Sms\Clients;

use Agentcis\Core\AbstractResource;
use Illuminate\Support\Arr;

class SmsRegistrationFormClient extends AbstractResource
{
    public function updateStatus(array $details)
    {
        $uri = "/api/v2/regulatory-bundles/nepal/webhook/";

        $formattedPayload = [];
        $newPayload = Arr::except($details, 'attachments');

        foreach ($newPayload as $key => $payload) {
            $formattedPayload[] = [
                'name' => $key,
                'contents' => $payload
            ];
        }

        if (!empty($details['attachments'])) {
            foreach ($details['attachments'] as $file) {
                $formattedPayload[] = [
                    'name' => 'attachments[]',
                    'contents' => \GuzzleHttp\Psr7\stream_for(fopen($file->path(), 'r')),
                    'Mime-Type' => $file->getMimeType(),
                    'filename' => $file->getClientOriginalName()
                ];
            }
        }

        return $this->postFormData($uri, $formattedPayload, true);
    }
}
