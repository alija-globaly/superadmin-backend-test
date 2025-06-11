<?php

namespace Agentcis\PartnerDatabase\Events;

use Agentcis\PartnerDatabase\Model\Branch;
use Illuminate\Foundation\Events\Dispatchable;

class BranchDeleted
{
    use Dispatchable;
    /**
     * @var Branch
     */
    private $branch;

    /**
     * BranchDeleted constructor.
     * @param Branch $branch
     */
    public function __construct(Branch $branch)
    {
        $this->branch = $branch;
    }

    public function getBranch()
    {
        return $this->branch;
    }
}
