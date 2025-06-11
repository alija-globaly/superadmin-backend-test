<?php

namespace Agentcis\PartnerDatabase\Resources;

use Illuminate\Http\Resources\Json\Resource;

class MasterCategoryResource extends Resource
{
    public function toArray($request)
    {
        return [
              'id' =>$this->id,
              'name' =>$this->name
        ];
    }
}
