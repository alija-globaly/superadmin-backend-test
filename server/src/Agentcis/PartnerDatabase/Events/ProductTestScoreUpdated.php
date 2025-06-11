<?php

namespace Agentcis\PartnerDatabase\Events;

use Agentcis\PartnerDatabase\Model\Product;
use Illuminate\Foundation\Events\Dispatchable;

class ProductTestScoreUpdated
{
    use Dispatchable;
    /**
     * @var Product
     */
    private $product;

    /**
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getProduct()
    {
        return $this->product;
    }
}
