<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Events\BranchStatusUpdated;
use Agentcis\PartnerDatabase\Model\Branch;
use Illuminate\Http\JsonResponse;

class DeActivatePartnerBranchAction
{
    public function __invoke($branchId)
    {
        /** @var Branch $branch */
        $branch = Branch::query()->where('id', (int) $branchId)->first();
        $branch->delete();

        BranchStatusUpdated::dispatch($branch);

        return new JsonResponse();
    }
}
