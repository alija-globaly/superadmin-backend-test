<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Model\Branch;
use Agentcis\PartnerDatabase\Model\Category;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryListAction
{
    public function __invoke(Request $request)
    {
        $includeChildren = ($request->query('include') === 'children');
        $categories = QueryBuilder::for(Category::query()->whereIsRoot()->when($includeChildren,
            function ($query) use ($request) {
                $query->with([
                    'children' => function ($query) use ($request) {
                        $type = $request->query('filter')['children.type'];
                        $query->when(in_array($type, ['partner', 'product']), function ($query) use ($type) {
                            $query->typeFilter($type);
                        });
                    }
                ]);
            })->when(!empty($request->query('filter')['id']), function ($query) use ($request) {
            return $query->where('id', $request->query('filter')['id']);
        }))->get();

        return ['data' => $categories];
    }
}
