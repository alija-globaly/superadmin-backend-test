<?php

namespace Agentcis\PartnerDatabase;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class ProductFeeCollection implements Jsonable, Arrayable
{
    private $fees = [];

    public function add(ProductFee $productFee)
    {
        $this->fees[] = $productFee;
        return true;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->fees;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }
}
