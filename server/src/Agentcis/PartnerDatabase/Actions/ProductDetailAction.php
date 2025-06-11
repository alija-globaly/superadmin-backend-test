<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Model\Category;
use Agentcis\PartnerDatabase\Model\Partner;
use Agentcis\PartnerDatabase\Model\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\QueryBuilder;

class ProductDetailAction
{
    public function __invoke($productId)
    {
        $product = QueryBuilder::for(
            Product::query()->withTrashed()
                ->with(['category.master'])
                ->where('id', $productId)
        )
            ->allowedFields('branches.id', 'branches.name', 'branches.product_id', 'branches.branch_id')
            ->allowedIncludes(['branches'])
            ->first();
        return new class($product) extends JsonResource
        {
            public function toArray($request)
            {
                return $this->resource;
            }

            /**
             * Get additional data that should be returned with the resource array.
             *
             * @param  \Illuminate\Http\Request $request
             * @return array
             */
            public function with($request)
            {
                $partner = Partner::query()->withTrashed()->where('id', $this->resource->partner_id)->first([
                    'id',
                    'category_id'
                ]);
                /** @var Category $partnerCategory */
                $partnerCategory = Category::query()->where('id', $partner->category_id)->first();
                if ($partnerCategory->isRoot()) {
                    $partnerCategory->load([
                        'children' => function ($query) {
                            $query->where('type', 'product');
                        }
                    ]);
                } else {
                    $partnerCategory = Category::query()
                        ->with([
                            'children' => function ($query) {
                                $query->where('type', 'product');
                            }
                        ])
                        ->where('id', $partnerCategory->parent_id)
                        ->first();
                }
                return [
                    'category' => $partnerCategory,
                ];
            }
        };
    }
}
