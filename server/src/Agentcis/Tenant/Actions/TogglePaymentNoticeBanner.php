<?php

namespace Agentcis\Tenant\Actions;

use Agentcis\Tenant\Model\Tenant;
use Illuminate\Http\Resources\Json\JsonResource;

class TogglePaymentNoticeBanner
{
    public const PAYMENT_BANNER_STATUS = ['enabled', 'disabled'];

    public function __invoke(int $tenantId, $status): JsonResource
    {
        $tenant = Tenant::query()->findOrFail($tenantId);

        throw_if(!in_array($status, self::PAYMENT_BANNER_STATUS, true),
            new \Exception('Provided status is not allowed.'));
        $tenant->update([
            'meta' => json_encode([
                'payment_banner' => $status
            ], JSON_THROW_ON_ERROR)
        ]);

        return new JsonResource([
            'message' => "Successfully $status status."
        ]);
    }
}
