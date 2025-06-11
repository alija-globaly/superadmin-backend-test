<?php

namespace Agentcis\BlackListDomains\Actions;

use Agentcis\BlackListDomains\BlockedDomainRepository;
use Illuminate\Http\JsonResponse;

class DeleteDomainAction
{
    public function __invoke($domain, BlockedDomainRepository $blockedDomainRepository)
    {
        $blockedDomainRepository->delete($domain);

        return new JsonResponse();
    }
}
