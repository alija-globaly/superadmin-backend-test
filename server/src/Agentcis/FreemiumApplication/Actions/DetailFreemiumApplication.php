<?php

namespace Agentcis\FreemiumApplication\Actions;

use Agentcis\FreemiumApplication\FreemiumApplicationService;

class DetailFreemiumApplication
{
    public function __invoke($applicationId, FreemiumApplicationService $applicationService)
    {
        return $applicationService->findById($applicationId);
    }
}
