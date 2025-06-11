<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Events\BranchStatusUpdated;
use Agentcis\PartnerDatabase\Model\Branch;
use Illuminate\Http\JsonResponse;

class ActivatePartnerBranchAction
{
    public function __invoke($branchId)
    {
        /** @var Branch $branch */
        $branch = Branch::query()->withTrashed()->where('id', (int) $branchId)->first();
        $branch->restore();

        BranchStatusUpdated::dispatch($branch);
        return new JsonResponse();
    }
}
