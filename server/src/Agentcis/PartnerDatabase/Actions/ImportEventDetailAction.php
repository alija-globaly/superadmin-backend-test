<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Model\ImportEvent;
use Illuminate\Http\Resources\Json\Resource;

class ImportEventDetailAction
{
    public function __invoke($eventId)
    {
        $events = ImportEvent::query()
            ->with('user')
            ->select('id', 'created_at', 'updated_at', 'user_id', 'status', 'report', 'file_path')
            ->find($eventId);

        return new class($events) extends Resource {
            public function toArray($request)
            {
                return $this->resource;
            }
        };
    }
}
