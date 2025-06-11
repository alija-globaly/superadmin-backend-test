<?php

namespace Agentcis\PartnerDatabase\Handlers;

use Agentcis\PartnerDatabase\Events\NewProductDetailAdded;
use Agentcis\PartnerDatabase\WebHookEvent;
use Agentcis\PartnerDatabase\WebHookStatus;
use Agentcis\WebhookEvent\Model\Event;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class SendProductAddedNotification
{
    public function handle(NewProductDetailAdded $productDetailAdded)
    {
        $product = $productDetailAdded->getProduct()->fresh([
            'branches',
            'partner' => function ($query) {
                return $query->withTrashed()->select('id', 'name', 'deleted_at');
            }
        ]);

        // save event to database
        $event = new Event([
            'id' => Str::orderedUuid(),
            'event_name' => WebHookEvent::PARTNER_PRODUCT_ADDED,
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
                    'event_name' => WebHookEvent::PARTNER_PRODUCT_ADDED,
                    'data' => $product,
                ])
                ->dispatch();
        }
    }
}
