<?php

namespace Agentcis\PartnerDatabase\Handlers;

use Agentcis\PartnerDatabase\Events\ProductDetailUpdated;
use Agentcis\PartnerDatabase\Events\ProductFeeUpdated;
use Agentcis\PartnerDatabase\Events\ProductStatusUpdated;
use Agentcis\PartnerDatabase\Events\ProductTestScoreUpdated;
use Agentcis\PartnerDatabase\WebHookEvent;
use Agentcis\PartnerDatabase\WebHookStatus;
use Agentcis\WebhookEvent\Model\Event;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class SendProductTestScoreUpdatedNotification
{
    public function handle(ProductTestScoreUpdated $productTestScoreUpdated)
    {
        $product = $productTestScoreUpdated->getProduct()->fresh([
            'partner' => function ($query) {
                return $query->withTrashed()->select('id', 'name', 'deleted_at');
            }
        ]);

        $event = new Event([
            'id' => Str::orderedUuid(),
            'event_name' => WebHookEvent::PARTNER_PRODUCT_TEST_SCORE_UPDATED,
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
                    'event_name' => WebHookEvent::PARTNER_PRODUCT_TEST_SCORE_UPDATED,
                    'data' => $product,
                ])
                ->dispatch();
        }
    }
}
