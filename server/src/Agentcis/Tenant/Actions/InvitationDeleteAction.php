<?php

namespace Agentcis\Tenant\Actions;

use Agentcis\Tenant\TenantInvitationService;

class InvitationDeleteAction
{
    public function __invoke($invitationId, TenantInvitationService $invitationService)
    {
        return $invitationService->delete($invitationId);
    }
}
