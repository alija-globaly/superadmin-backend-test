<?php

namespace Agentcis\FreemiumApplication\Actions;

use Agentcis\FreemiumApplication\FreemiumApplicationService;
use Agentcis\FreemiumApplication\Model\FreemiumApplication;
use Agentcis\Tenant\Model\Invitation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

class ListFreemiumApplication
{
    public function __invoke(Request $request)
    {
        $applications = QueryBuilder::for(FreemiumApplication::query()
            ->when($request->query('status'), function ($query) use ($request) {
                $query->where('status', $request->query('status'));
            }))
            ->jsonPaginate();

        return new class($applications) extends ResourceCollection {
            public function toArray($request)
            {
                return $this->collection;
            }
        };
    }
}
