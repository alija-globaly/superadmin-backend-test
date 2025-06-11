<?php

namespace Agentcis\Tenant;

use Illuminate\Cache\Repository;

class RedisTenantRepository
{
    /**
     * @var Repository
     */
    private $cache;

    const CACHE_KEY = 'tenant_%s_detail';

    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    public function findById($tenantId)
    {
        return $this->cache->get(sprintf(self::CACHE_KEY, $tenantId));
    }
}
