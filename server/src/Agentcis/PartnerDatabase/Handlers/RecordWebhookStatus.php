<?php

namespace Agentcis\PartnerDatabase\Handlers;

use Agentcis\PartnerDatabase\WebHookStatus;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallEvent;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class RecordWebhookStatus
{
    public function handle(WebhookCallEvent $webhookCallEvent)
    {
        $eventId = Arr::get($webhookCallEvent->payload, 'event_id');

        switch (true) {
            case $webhookCallEvent instanceof WebhookCallFailedEvent :
            case $webhookCallEvent instanceof FinalWebhookCallFailedEvent :

                DB::table('webhook_notification_history')
                    ->insert([
                        'id' => Str::orderedUuid(),
                        'webhook_notification_id' => $eventId,
                        'webhook_listener_url' => $webhookCallEvent->webhookUrl,
                        'response' => json_encode([
                            'error_message' => $webhookCallEvent->errorMessage,
                            'headers' => $webhookCallEvent->response->getHeaders(),
                            'status_code' => $webhookCallEvent->response->getStatusCode(),
                        ], JSON_OBJECT_AS_ARRAY),
                        'status' => WebHookStatus::FAILED,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                DB::table('webhook_notifications')
                    ->where('id', $eventId)
                    ->update([
                        'status' => WebHookStatus::FAILED
                    ]);
            case $webhookCallEvent instanceof WebhookCallSucceededEvent :
                DB::table('webhook_notification_history')
                    ->insert([
                        'id' => Str::orderedUuid(),
                        'webhook_notification_id' => $eventId,
                        'webhook_listener_url' => $webhookCallEvent->webhookUrl,
                        'response' => json_encode([
//                            'body' => $webhookCallEvent->response->getBody()->getContents(),
                            'body' => json_decode($webhookCallEvent->response->getBody()->getContents(), true),
                            'headers' => $webhookCallEvent->response->getHeaders(),
                            'status_code' => $webhookCallEvent->response->getStatusCode(),
                        ], JSON_OBJECT_AS_ARRAY),
                        'status' => WebHookStatus::SUCCESS,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                DB::table('webhook_notifications')
                    ->where('id', $eventId)
                    ->update([
                        'status' => WebHookStatus::SUCCESS
                    ]);
        }
    }
}
