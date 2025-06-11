<?php

namespace Agentcis\PartnerDatabase\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    public function toArray($request)
    {
        return $this->resource;
    }
}
