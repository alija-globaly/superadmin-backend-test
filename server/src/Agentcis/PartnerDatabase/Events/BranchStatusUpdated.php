<?php

namespace Agentcis\PartnerDatabase\Events;

use Agentcis\PartnerDatabase\Model\Branch;
use Illuminate\Foundation\Events\Dispatchable;

class BranchStatusUpdated
{
    use Dispatchable;
    /**
     * @var Branch
     */
    private $branch;

    /**
     * BranchStatusUpdated constructor.
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
