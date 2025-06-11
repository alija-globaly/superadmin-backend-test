<?php

namespace Agentcis\Tenant\Actions;

use Agentcis\Tenant\Model\Tenant;
use Illuminate\Http\JsonResponse;

class ListTenantConfigOptions
{
    public function __invoke(int $tenantId)
    {
        $tenant = Tenant::query()->findOrFail($tenantId, ['id', 'meta']);

        return new JsonResponse([
            'data' => [
                'id' => $tenant->id,
                'meta' => $tenant->meta ? json_decode($tenant->meta, false): ['payment_banner' => 'enabled'],
            ]
        ]);
    }
}
