<?php

namespace Agentcis\PartnerDatabase\Actions;

use Agentcis\PartnerDatabase\Events\BranchDeleted;
use Agentcis\PartnerDatabase\Model\Branch;

class BranchDeleteAction
{
    public function __invoke($branchId)
    {
        /** @var Branch $branch */
        $branch = Branch::query()->withTrashed()->where('id', $branchId)->first();
//        if($branch->getAttribute('type') == 'Head Office') {
//            abort(404, "Head Office can't be deleted.");
//        }
        BranchDeleted::dispatch($branch);

        $branch->forceDelete();
    }
}
