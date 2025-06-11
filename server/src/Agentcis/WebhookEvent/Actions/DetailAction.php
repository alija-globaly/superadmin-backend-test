<?php

namespace Agentcis\WebhookEvent\Actions;

use Agentcis\WebhookEvent\Model\Event;

class DetailAction
{
    public function __invoke($id)
    {
        $event = Event::query()->with('history')->where('id', $id)->first();
        $event->test = $event->history->groupBy('webhook_listener_url');
        return [
            'data' => $event,
        ];
    }
}
