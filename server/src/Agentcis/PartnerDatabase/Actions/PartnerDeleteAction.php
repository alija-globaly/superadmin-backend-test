<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Events\PartnerDeleted;
use Agentcis\PartnerDatabase\Model\Partner;
use Illuminate\Http\JsonResponse;

class PartnerDeleteAction
{
    public function __invoke($partnerId)
    {
        /** @var Partner $partner */
        $partner = Partner::query()
            ->withTrashed()
            ->where('id', $partnerId)
            ->first();

        $partner->forceDelete();

        PartnerDeleted::dispatch($partner);

        return new JsonResponse();

    }
}
