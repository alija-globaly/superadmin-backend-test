<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Events\ProductStatusUpdated;
use Agentcis\PartnerDatabase\Model\Partner;
use Agentcis\PartnerDatabase\Model\Product;
use Illuminate\Http\JsonResponse;

class ActivateProductAction
{
    public function __invoke($productId)
    {
        /** @var Product $product */
        $product = Product::query()->withTrashed()->where('id', (int) $productId)->first();
        $product->restore();

        ProductStatusUpdated::dispatch($product);
        return new JsonResponse();
    }
}
