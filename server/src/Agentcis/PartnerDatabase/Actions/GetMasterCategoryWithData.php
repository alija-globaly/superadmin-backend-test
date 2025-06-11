<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Model\Category;
use Agentcis\PartnerDatabase\Model\Partner;
use Agentcis\PartnerDatabase\Resources\MasterCategoryCollection;

class GetMasterCategoryWithData
{

    public function __invoke(Partner $partner, Category $category)
    {
        $categoryDetails = $partner->newQuery()
            ->has('products')
            ->join('categories', 'categories.id', '=', 'partners.category_id')
            ->select(
                'categories.parent_id'
            )
            ->distinct()
            ->get()
            ->map(function ($query) use ($category) {
                return $category->select('id', 'name')->where('id', $query->parent_id)->first();
            });

        return new MasterCategoryCollection($categoryDetails);
    }
}
