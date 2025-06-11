<?php

namespace Agentcis\PartnerDatabase\Handlers;

use Agentcis\PartnerDatabase\Events\ProductDeleted;
use Agentcis\PartnerDatabase\WebHookEvent;
use Agentcis\PartnerDatabase\WebHookStatus;
use Agentcis\WebhookEvent\Model\Event;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class SendProductDeletedNotification
{
    public function handle(ProductDeleted $productDeleted)
    {
        $product = $productDeleted->getProduct();
        $event = new Event([
            'id' => Str::orderedUuid(),
            'event_name' => WebHookEvent::PARTNER_PRODUCT_DELETED,
            'payload' => $product,
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
                    'event_name' => WebHookEvent::PARTNER_PRODUCT_DELETED,
                    'data' => $product,
                ])
                ->dispatch();
        }
    }
}
