<?php

namespace Agentcis\PartnerDatabase\Handlers;

use Agentcis\PartnerDatabase\Events\NewBranchAdded;
use Agentcis\PartnerDatabase\WebHookEvent;
use Agentcis\PartnerDatabase\WebHookStatus;
use Agentcis\WebhookEvent\Model\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class SendBranchAddedNotification
{
    public function handle(NewBranchAdded $branchAdded)
    {
        $branch = $branchAdded->getBranch();
        $payload = $branch->fresh([
            'partner' => function ($query) {
                return $query->withTrashed()->select('id', 'name', 'deleted_at');
            }
        ]);
        $event = new Event([
            'id' => Str::orderedUuid(),
            'event_name' => WebHookEvent::PARTNER_BRANCH_ADDED,
            'payload' => $payload,
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
                    'event_name' => WebHookEvent::PARTNER_BRANCH_ADDED,
                    'data' => $payload,
                ])
                ->dispatch();
        }
    }
}
