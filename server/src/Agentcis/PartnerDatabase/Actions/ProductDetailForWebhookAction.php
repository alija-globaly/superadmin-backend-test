<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Model\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\QueryBuilder;

class ProductDetailForWebhookAction
{
    public function __invoke($productId)
    {
        $product = QueryBuilder::for(
            Product::query()
                ->where('id', $productId)
        )
            ->allowedIncludes(['branches'])
            ->first();
        return new class($product) extends JsonResource
        {
            public function toArray($request)
            {
                return $this->resource;
            }
        };
    }
}
