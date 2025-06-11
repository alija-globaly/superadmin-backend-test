<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Resources\PartnerCollection;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Agentcis\PartnerDatabase\Model\Partner;

class PartnersListAction
{
    public function __invoke(Request  $request)
    {
        $search = $request->query('search');
        $partner = QueryBuilder::for(
            Partner::query()
                ->withTrashed()
                ->with(['category.master'])
                ->when($search, function ($query, $search) {
                    $query->whereRaw("partners.name LIKE ?", ["%$search%"]);
                })
                ->withCount('products', 'branches')
                ->orderBy('name')
        )->allowedIncludes(['country'])
            ->jsonPaginate();

        return new PartnerCollection($partner);
    }
}
