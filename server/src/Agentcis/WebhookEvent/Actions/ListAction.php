<?php

namespace Agentcis\WebhookEvent\Actions;

use Agentcis\WebhookEvent\Model\Event;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

class ListAction
{
    public function __invoke()
    {
        $events = QueryBuilder::for(
            Event::query()->latest()
        )->jsonPaginate();

        return new class($events) extends ResourceCollection{};
    }
}
