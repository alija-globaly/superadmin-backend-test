<?php

namespace Agentcis\PartnerDatabase\Events;

use Agentcis\PartnerDatabase\Model\Partner;

class PartnerAdded
{
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
