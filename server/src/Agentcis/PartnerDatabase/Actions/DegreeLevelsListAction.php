<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\DegreeLevels;

class DegreeLevelsListAction
{
    public function __invoke()
    {
        $degreeLevels = (new DegreeLevels)->toArray();
        return [
            'data' => $degreeLevels,
        ];
    }
}
