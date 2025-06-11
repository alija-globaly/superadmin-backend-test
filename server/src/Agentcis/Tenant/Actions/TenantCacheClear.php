<?php

namespace Agentcis\Tenant\Actions;

use Agentcis\AgentcisClient;
use Agentcis\Tenant\Model\Tenant;
use Illuminate\Http\JsonResponse;

class TenantCacheClear
{
    public function __invoke(int $tenantId): JsonResponse
    {
        $tenant = Tenant::query()->findOrFail($tenantId);
        $url = sprintf("https://%s.%s", $tenant->subdomain, env('AGENTCISAPP_DOMAIN'));
        $agentcisClient = new AgentcisClient($url);

        $agentcisClient->tenantConfig()->clearCache([
            'tenant_id' => $tenantId
        ]);
        return new JsonResponse([
            'message' => 'Successfully cleared cache.'
        ]);
    }
}
