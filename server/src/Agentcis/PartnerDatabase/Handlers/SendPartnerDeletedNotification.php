<?php

namespace Agentcis\PartnerDatabase\Handlers;

use Agentcis\PartnerDatabase\Events\PartnerDeleted;
use Agentcis\PartnerDatabase\WebHookEvent;
use Agentcis\PartnerDatabase\WebHookStatus;
use Agentcis\WebhookEvent\Model\Event;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class SendPartnerDeletedNotification
{
    /**
     * @param PartnerDeleted $partnerDeleted
     * @throws \Spatie\WebhookServer\Exceptions\CouldNotCallWebhook
     */
    public function handle(PartnerDeleted $partnerDeleted)
    {
        $partner = $partnerDeleted->getPartner();

        $event = new Event([
                'id' => Str::orderedUuid(),
                'event_name' => WebHookEvent::PARTNER_DELETED,
                'payload' => $partner,
                'status' => WebHookStatus::PROCESSING,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $event->save();

        foreach (config('services.webhook_listeners') as $listeners) {
            WebhookCall::create()
                ->url($listeners['url'])
                ->useSecret($listeners['secret'])
                ->payload([
                    'event_id' => $event->id,
                    'event_name' => WebHookEvent::PARTNER_DELETED,
                    'data' => $partner,
                ])
                ->dispatch();
        }
    }
}
