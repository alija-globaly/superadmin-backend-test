<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Resources\PartnerResource;
use Spatie\QueryBuilder\QueryBuilder;
use Agentcis\PartnerDatabase\Model\Partner;

class PartnerDetailAction
{
    public function __invoke($partnerId)
    {
        $partner = QueryBuilder::for(Partner::query()->withTrashed()->withCount('branches')->withCount('products')->with('category.master')->where('id', $partnerId))
            ->allowedIncludes(['branches', 'products', 'country'])
            ->first();

        return new PartnerResource($partner);
    }
}
