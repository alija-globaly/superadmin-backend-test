<?php

namespace Agentcis\PartnerDatabase\Events;

use Agentcis\PartnerDatabase\Model\Product;
use Illuminate\Foundation\Events\Dispatchable;

class NewProductDetailAdded
{
    use Dispatchable;
    /**
     * @var Product
     */
    private $product;

    /**
     * NewProductDetailAdded constructor.
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
