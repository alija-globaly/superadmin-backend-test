<?php
namespace Agentcis\PartnerDatabase\Events;

use Agentcis\PartnerDatabase\Model\Branch;
use Illuminate\Foundation\Events\Dispatchable;

class NewBranchAdded
{
    use Dispatchable;
    /**
     * @var Branch
     */
    private $branch;

    /**
     * NewBranchAdded constructor.
     * @param Branch $branch
     */
    public function __construct(Branch $branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return Branch
     */
    public function getBranch(): Branch
    {
        return $this->branch;
    }
}
