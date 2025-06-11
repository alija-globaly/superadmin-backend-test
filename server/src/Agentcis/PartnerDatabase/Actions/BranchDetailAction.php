<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Model\Branch;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\QueryBuilder;

class BranchDetailAction
{
    public function __invoke($branchId)
    {
        $branch = QueryBuilder::for(Branch::query()->withTrashed()->where('id', $branchId))
            ->allowedIncludes(['country'])
            ->first();

        return new class($branch) extends JsonResource
        {
            public function toArray($request)
            {
                return $this->resource;
            }
        };
    }
}
