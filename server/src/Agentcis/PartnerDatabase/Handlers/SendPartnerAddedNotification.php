<?php

namespace Agentcis\PartnerDatabase\Handlers;

use Agentcis\PartnerDatabase\Events\PartnerAdded;
use Agentcis\PartnerDatabase\WebHookEvent;
use Agentcis\WebhookEvent\Model\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class SendPartnerAddedNotification
{
    public function handle(PartnerAdded $partnerAddedEvent)
    {
        // not being used as of now
        return;
//        $partner = $partnerAddedEvent->getPartner();
//
//        // save event to database
//        $event = new Event([
//                'id' => Str::orderedUuid(),
//                'name' => WebHookEvent::PARTNER_,
//                'payload' => $partner,
//                'created_at' => now(),
//                'updated_at' => now(),
//
//            ]);
//        foreach (config('services.webhook_listeners') as $listeners) {
//            WebhookCall::create()
//                ->url($listeners['url'])
//                ->useSecret($listeners['secret'])
//                ->payload([
//                    'event_id' => $event->id,
//                    'event_name' => WebHookEvent::PARTNER_BRANCH_STATUS_CHANGED,
//                    'data' => $partner,
//                ])
//                ->dispatch();
//        }
    }
}
