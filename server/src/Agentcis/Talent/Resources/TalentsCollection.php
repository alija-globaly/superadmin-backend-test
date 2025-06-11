<?php

namespace Agentcis\Talent\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TalentsCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection;
    }
}
