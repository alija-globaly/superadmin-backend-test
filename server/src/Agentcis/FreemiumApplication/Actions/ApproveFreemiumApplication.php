<?php

namespace Agentcis\FreemiumApplication\Actions;

use Agentcis\FreemiumApplication\FreemiumApplicationService;

class ApproveFreemiumApplication
{
    public function __invoke($applicationId, FreemiumApplicationService $applicationService)
    {
        return $applicationService->approve($applicationId);
    }
}
