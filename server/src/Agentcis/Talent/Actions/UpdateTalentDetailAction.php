<?php

namespace Agentcis\Talent\Actions;

use App\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UpdateTalentDetailAction
{
    use ValidatesRequests;

    public function __invoke($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => ['required', 'email:rfc,dns', Rule::unique('users', 'email')->ignore($id),],
            'password' => 'nullable|min:8',
            'role' => 'required',
        ]);
        $user = User::query()->find($id);
        $user->fill($request->only('name', 'email'));
        if((int)$id !== $request->user()->id) {
            $user->fill([ 'role' => $request->get('role')]);
        }
        if (!empty($request->input('password'))) {
            $user->forceFill([
                'password' => bcrypt($request->input('password')),
            ]);
        }
        $user->save();

        return new JsonResponse(['data' => $user->fresh()], Response::HTTP_ACCEPTED);
    }
}
