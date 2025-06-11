<?php

namespace Agentcis\Tenant\Actions;

use Agentcis\Tenant\Model\Tenant;
use Agentcis\Tenant\RedisTenantRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class DetailAction
{
    /**
     * @var RedisTenantRepository
     */
    private $tenantRepository;

    /**
     * @param RedisTenantRepository $tenantRepository
     */
    public function __construct(RedisTenantRepository $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }
    public function __invoke($identifier)
    {
        $tenant = QueryBuilder::for(
            Tenant::query()->where('tenants.id', $identifier)
                ->select(
                    'tenants.id',
                    'tenants.business_name',
                    'tenants.first_name',
                    'tenants.last_name',
                    'tenants.email',
                    'tenants.subdomain',
                    'tenants.db_name',
                    'tenants.phone_number',
                    'tenants.country',
                    'tenants.plan_code',
                    'tenants.subscription_status',
                    'tenants.payment_currency',
                    'tenants.applicable_for_special_discount',
                    'tenants.created_at',
                    'tenants.updated_at',
                    'tenants.zoho_id as zoho_customer_id')
        )
            ->first();
        $tenant->subscription = optional($this->tenantRepository->findById($tenant->id))->getSubscription();
        return new class($tenant) extends JsonResource
        {
            public function toArray($request)
            {
                $tenant = $this->resource;
                return [
                    'id' => $tenant->id,
                    'business_name' => $tenant->business_name,
                    'email' => $tenant->email,
                    'name' => $tenant->first_name . ' ' . $tenant->last_name,
                    'fqdn' => sprintf('https://%s.%s', $tenant->subdomain, config('services.agentcisapp.domain')),
                    'country' => $tenant->country,
                    'phone_number' => $tenant->phone_number,
                    'db_name' => $tenant->db_name,
                    'customer_id' => $tenant->customer_id,
                    'status' => $tenant->subscription_status,
                    'subscription_id' => $tenant->subscription_id,
                    'subscription' => $tenant->subscription,
                    'plan_name' => $tenant->plan_code,
                    'created_at' => $tenant->created_at,
                    'started_at' => $tenant->created_at,
                    'updated_at' => $tenant->updated_at,
                ];
            }
        };
    }
}
