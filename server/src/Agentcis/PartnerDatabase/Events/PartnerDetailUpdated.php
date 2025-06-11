<?php

namespace Agentcis\PartnerDatabase\Events;

use Agentcis\PartnerDatabase\Model\Partner;
use Illuminate\Foundation\Events\Dispatchable;

class PartnerDetailUpdated
{
    use Dispatchable;

    /**
     * @var Partner
     */
    private $partner;

    public function __construct(Partner $partner)
    {
        $this->partner = $partner;
    }

    public function getPartner()
    {
        return $this->partner;
    }
}
