<?php


namespace Agentcis\PartnerDatabase\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class MasterCategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($category) {
            return new MasterCategoryResource($category);
        });
    }

}
