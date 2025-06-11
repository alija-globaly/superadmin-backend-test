<?php

namespace Agentcis\PartnerDatabase\Handlers;

use Agentcis\PartnerDatabase\Events\BranchDeleted;
use Agentcis\PartnerDatabase\Events\BranchDetailUpdated;
use Agentcis\PartnerDatabase\WebHookEvent;
use Agentcis\PartnerDatabase\WebHookStatus;
use Agentcis\WebhookEvent\Model\Event;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class SendBranchDeletedNotification
{
    public function handle(BranchDeleted $branchDeleted)
    {
        $branch = $branchDeleted->getBranch();
        $event = new Event([
            'id' => Str::orderedUuid(),
            'event_name' => WebHookEvent::PARTNER_BRANCH_DELETED,
            'payload' => $branch,
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
                    'event_name' => WebHookEvent::PARTNER_BRANCH_DELETED,
                    'data' => $branch,
                ])
                ->dispatch();
        }
    }
}
