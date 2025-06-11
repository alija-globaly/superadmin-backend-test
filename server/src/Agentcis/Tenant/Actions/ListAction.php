<?php

namespace Agentcis\Tenant\Actions;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;
use Agentcis\Tenant\Model\Tenant;

class ListAction
{
    public function __invoke()
    {
        $tenants = QueryBuilder::for(
            Tenant::query()->latest('tenants.created_at')
                ->select('tenants.id', 'tenants.business_name', 'tenants.email', 'tenants.subdomain',
                    'tenants.phone_number', 'tenants.created_at', 'tenants.customer_id', 'tenants.subscription_id',
                    'tenants.plan_code', 'tenants.subscription_status as status')
        )->jsonPaginate();


        return new class($tenants) extends ResourceCollection {
            public function toArray($request)
            {
                return $this->collection->map(function ($tenant) {
                    $tenant->fqdn = sprintf('https://%s.%s', $tenant->subdomain, config('services.agentcisapp.domain'));
                    $tenant->plan_name = $tenant->plan_code;
                    return $tenant;
                });
            }
        };
    }
}
