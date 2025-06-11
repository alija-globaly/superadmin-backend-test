<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Model\Branch;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PartnerBranchesListAction
{
    public function __invoke($partnerId)
    {
        $branches = Branch::query()
            ->withTrashed()
            ->with('country')
            ->where('partner_id', (int)$partnerId)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return new class($branches) extends ResourceCollection
        {
            public function toArray($request)
            {
                return $this->collection;
            }
        };
    }
}
