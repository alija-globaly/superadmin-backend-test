<?php

namespace Agentcis\Talent\Actions;

use App\User;
use Illuminate\Http\Request;

class TalentDetailAction
{
    public function __invoke($id, Request $request)
    {
        return ['data' => User::query()->where('id', $id)->first() ];
    }

}
