<?php

namespace Agentcis\PartnerDatabase\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PartnerCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection;
    }
}
