<?php

namespace Agentcis\Tenant\Actions;

use Agentcis\Tenant\DTOs\InvitationDTO;
use Agentcis\Tenant\TenantInvitationService;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

class InviteTenantAction
{
    use ValidatesRequests;

    public function __invoke(Request $request, TenantInvitationService $invitationService)
    {
        $this->validate($request, [
            'email' => 'bail|required|email:rfc,dns',
        ]);

        $currentUser = $request->user() ? $request->user()->getAttribute('name') : null;
        $isPaid = $request->get('type') === 'paid';
        $invitationDTO = new InvitationDTO(
            $request->get('email'),
            $isPaid,
            $request->get('plan'),
            $request->get('currency'),
            $request->get('billing_cycle'),
            $currentUser
        );

        return $invitationService->send($invitationDTO);
    }
}
