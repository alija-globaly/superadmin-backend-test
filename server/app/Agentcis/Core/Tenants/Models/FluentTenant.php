<?php

namespace App\Agentcis\Core\Tenants\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class FluentTenant extends Fluent
{
    protected $tenant;
    private $features;
    private $subscription;

    public function __construct($tenant, Collection $features, $subscription)
    {
        parent::__construct($tenant->toArray());
        $this->tenant = $tenant;
        $this->features = $features;
        $this->subscription = $subscription;
    }

    /**
     * @return Tenant
     */
    public function getInstance(): Tenant
    {
        return $this->tenant;
    }

    /**
     * @return Collection
     */
    public function getFeatures(): Collection
    {
        return $this->features;
    }

    /**
     * @return SubscriptionDetail
     */
    public function getSubscription()
    {
        return $this->subscription;
    }
}
