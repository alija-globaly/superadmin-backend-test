<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

class PartnerProductsListAction
{
    public function __invoke($partnerId, Request  $request)
    {
        $search =  $request->query->get('search');
        $products = QueryBuilder::for(
            Product::query()
                ->withTrashed()
                ->with(['category.master'])
                ->where('partner_id', (int)$partnerId)
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->select('id', 'name', 'deleted_at')
                ->orderBy('name')
        )
            ->allowedIncludes(['branches'])
            ->jsonPaginate();

        return new class($products) extends ResourceCollection
        {
            public function toArray($request)
            {
                return $this->collection;
            }
        };
    }
}
