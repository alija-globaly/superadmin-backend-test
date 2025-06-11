<?php

namespace Agentcis\PartnerDatabase\Handlers;

use Agentcis\PartnerDatabase\Events\PartnerStatusChanged;
use Agentcis\PartnerDatabase\WebHookEvent;
use Agentcis\PartnerDatabase\WebHookStatus;
use Agentcis\WebhookEvent\Model\Event;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class SendPartnerStatusChangedNotification
{
    public function handle(PartnerStatusChanged $partnerStatusChanged)
    {
        $partner = $partnerStatusChanged->getPartner();

        $event = new Event([
                'id' => Str::orderedUuid(),
                'event_name' => WebHookEvent::PARTNER_STATUS_CHANGED,
                'payload' => $partner,
                'status' => WebHookStatus::PROCESSING,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $event->save();
        foreach (config('services.webhook_listeners') as $listeners) {
            WebhookCall::create()
                ->url($listeners['url'])
                ->doNotVerifySsl()
                ->useSecret($listeners['secret'])
                ->payload([
                    'event_id' => $event->id,
                    'event_name' => WebHookEvent::PARTNER_STATUS_CHANGED,
                    'data' => $partner,
                ])
                ->dispatch();
        }
    }
}
