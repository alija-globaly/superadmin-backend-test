<?php

namespace Agentcis\Talent\Actions;

use Agentcis\Talent\Resources\TalentsCollection;
use App\User;
use Spatie\QueryBuilder\QueryBuilder;

class TalentListAction
{
    public function __invoke()
    {
        $talents = QueryBuilder::for(User::query()->orderBy('name'))
            ->jsonPaginate();

        return new TalentsCollection($talents);
    }
}
