<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Model\Country;
use Agentcis\PartnerDatabase\Model\Partner;
use Illuminate\Http\Request;

class AvailableCountriesListAction
{
    public function __invoke(Request $request)
    {
        $categoryId = $request->input('category');
        $countryIds = Partner::query()
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->join('categories', 'categories.id', '=', 'partners.category_id');
                $query->where('categories.parent_id', '=', $categoryId);
            })
            ->select('country')
            ->groupBy('country')
            ->get();

        return [
            'data' => Country::query()
                ->whereIn('id', $countryIds)
                ->get()
        ];
    }
}
