<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Model\ImportEvent;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ImportEventListAction
{
    public function __invoke()
    {
        $events = ImportEvent::query()
            ->latest()->with('user')
            ->select('id', 'created_at', 'updated_at','user_id', 'status')
            ->paginate();

        return new class($events) extends ResourceCollection {
            public function toArray($request)
            {
                return $this->collection;
            }
        };
    }
}
