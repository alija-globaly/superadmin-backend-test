<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Events\PartnerStatusChanged;
use Agentcis\PartnerDatabase\Model\Partner;
use Illuminate\Http\JsonResponse;

class ActivatePartnerAction
{
    public function __invoke($partnerId)
    {
        /** @var Partner $partner */
        $partner = Partner::query()->withTrashed()->where('id', (int) $partnerId)->first();
        $partner->restore();

        PartnerStatusChanged::dispatch($partner);
        return new JsonResponse();
    }
}
