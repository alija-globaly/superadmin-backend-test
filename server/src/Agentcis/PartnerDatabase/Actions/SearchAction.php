<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Filters;
use Agentcis\PartnerDatabase\Model\Partner;
use Agentcis\PartnerDatabase\Resources\PartnerCollection;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\QueryBuilderRequest;

class SearchAction
{
    public function __invoke(Request $request)
    {
        $requestedIncludes = collect(explode(',', $request->query('include')));
        $partner = QueryBuilder::for(
            Partner::query()
                ->has('products.branches')
                ->with(['category.master'])
                ->withCount('products', 'branches')
                ->when(!empty($requestedIncludes->contains('products')), function ($query) {
                    $query->with([
                        'products' => function ($query) {
                            $query->withTrashed()->with([
                                'category',
                                'branches' => function ($query) {
                                    $query->withTrashed()->with(['country']);
                                }
                            ]);
                        }
                    ]);
                })
                ->when(!empty($requestedIncludes->contains('branches')), function ($query) {
                    $query->with([
                        'branches' => function ($query) {
                            $query->withTrashed();
                        }
                    ]);
                })
                ->when(!empty($requestedIncludes->contains('country')), function ($query) {
                    $query->with(['country']);
                })
                ->orderBy('name')
        )
            ->allowedFilters([
                AllowedFilter::custom('category', new Filters\FilterByCategory),
                AllowedFilter::custom('except_id', new Filters\ExcludePartnerIds),
                AllowedFilter::exact('id'),
                'name',
                'country',
            ])
            ->jsonPaginate();

        return new PartnerCollection($partner);
    }
}
