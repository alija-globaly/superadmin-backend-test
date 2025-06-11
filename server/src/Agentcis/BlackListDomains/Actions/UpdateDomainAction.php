<?php

namespace Agentcis\BlackListDomains\Actions;

use Agentcis\BlackListDomains\BlockedDomainRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateDomainAction
{
    public function __invoke(Request $request, BlockedDomainRepository $blockedDomainRepository)
    {
        $blockedDomainRepository->update($request->get('name'), $request->get('old_name'));

        return new JsonResponse();
    }
}
