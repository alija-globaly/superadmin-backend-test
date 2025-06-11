<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Events\PartnerStatusChanged;
use Agentcis\PartnerDatabase\Model\Partner;
use Illuminate\Http\JsonResponse;

class DeActivatePartnerAction
{
    public function __invoke($partnerId)
    {
        /** @var Partner $partner */
        $partner = Partner::query()->where('id', (int) $partnerId)->first();
        $partner->delete();

        PartnerStatusChanged::dispatch($partner);

        return new JsonResponse();
    }
}
