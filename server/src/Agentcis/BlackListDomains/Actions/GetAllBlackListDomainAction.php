<?php

namespace Agentcis\BlackListDomains\Actions;

use Agentcis\BlackListDomains\BlockedDomainRepository;

class GetAllBlackListDomainAction
{
    public function __invoke(BlockedDomainRepository $blockedDomainRepository)
    {
        return ['data' => $blockedDomainRepository->getAll()];
    }
}
