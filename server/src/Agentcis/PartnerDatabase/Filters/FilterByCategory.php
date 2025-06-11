<?php

namespace Agentcis\PartnerDatabase\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByCategory implements Filter
{

    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereHas('category.master', function (Builder $query) use ($value) {
            $query->where('name', $value);
        });
    }
}
