<?php

namespace Agentcis\PartnerDatabase\Handlers;

use Agentcis\PartnerDatabase\Events\PartnerDetailUpdated;
use Agentcis\PartnerDatabase\WebHookEvent;
use Agentcis\PartnerDatabase\WebHookStatus;
use Agentcis\WebhookEvent\Model\Event;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class SendPartnerDetailUpdatedNotification
{
    /**
     * @param PartnerDetailUpdated $partnerDetailUpdated
     * @throws \Spatie\WebhookServer\Exceptions\CouldNotCallWebhook
     */
    public function handle(PartnerDetailUpdated $partnerDetailUpdated)
    {
        $partner = $partnerDetailUpdated->getPartner()->fresh(['category.master']);

        $event = Event::create([
            'id' => Str::orderedUuid(),
            'event_name' => WebHookEvent::PARTNER_UPDATED,
            'payload' => $partner,
            'status' => WebHookStatus::PROCESSING,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (config('services.webhook_listeners') as $listeners) {
            WebhookCall::create()
                ->url($listeners['url'])
                ->doNotVerifySsl()
                ->timeoutInSeconds(30)
                ->useSecret($listeners['secret'])
                ->withTags(['event:'.WebHookEvent::PARTNER_UPDATED, 'partner:'. $partner->id])
                ->payload([
                    'event_id' => $event->id,
                    'event_name' => WebHookEvent::PARTNER_UPDATED,
                    'data' => $partner,
                ])
                ->dispatch();
        }
    }
}
