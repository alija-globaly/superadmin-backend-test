<?php

namespace Agentcis\Auth\Actions;

use Illuminate\Http\Request;

class LogoutAction
{
    public function __invoke(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
