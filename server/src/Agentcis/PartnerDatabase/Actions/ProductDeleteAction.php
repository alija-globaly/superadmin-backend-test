<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Events\ProductDeleted;
use Agentcis\PartnerDatabase\Model\Product;

class ProductDeleteAction
{
    public function __invoke($productId)
    {
        $product = Product::query()->where('id', $productId)->first();
        $product->forceDelete();

        ProductDeleted::dispatch($product);
    }
}
