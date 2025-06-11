<?php

namespace Agentcis\Core;

class TenantConfigChangerClient extends AbstractResource
{
    public function clearCache(array $details)
    {
        $uri = "/api/v2/cache-clear";
        return $this->get($uri);
    }
}
