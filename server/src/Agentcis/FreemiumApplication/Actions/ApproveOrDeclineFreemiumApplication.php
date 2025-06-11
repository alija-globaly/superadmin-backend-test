<?php

namespace Agentcis\FreemiumApplication\Actions;

use Agentcis\FreemiumApplication\FreemiumApplicationService;

class ApproveOrDeclineFreemiumApplication
{
    public function __invoke($applicationId, $status, FreemiumApplicationService $applicationService)
    {
        return $applicationService->changeStatus($applicationId, $status);
    }
}
