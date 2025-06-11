<?php

namespace Agentcis\Talent\Actions;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ToggleTalentStatus
{
    public function __invoke($id)
    {
        $user = User::query()->find($id);
        if ($user->id === auth()->user()->id) {
            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $user->forceFill([
            'status' => !$user->getAttribute('status')
        ]);
        $user->save();

        return new JsonResponse(['data' => $user], Response::HTTP_ACCEPTED);
    }
}
