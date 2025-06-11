<?php

namespace Agentcis\Talent\Actions;

use App\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;

class AddTalentDetail
{
    use ValidatesRequests;

    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:8',
            'role' => 'required',
        ]);
        $user = new User();
        $user->fill($request->only('name', 'email', 'role'));
        $user->forceFill([
            'status' => true,
            'password' => bcrypt($request->input('password')),
            'email_verified_at' => Date::now()
        ]);
        $user->save();

        return new JsonResponse(['data' => $user->fresh()], Response::HTTP_CREATED);
    }
}
