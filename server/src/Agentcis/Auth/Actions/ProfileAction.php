<?php

namespace Agentcis\Auth\Actions;


class ProfileAction
{
    public function __invoke()
    {
        return ['data' => request()->user()];
    }
}
