<?php

namespace Agentcis\PartnerDatabase\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class ExcludePartnerIds implements Filter
{

    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereNotIn('partners.id', $value);
    }
}
