<?php

namespace Agentcis\Auth\Actions;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LoginAction
{
    use ValidatesRequests;

    public function __invoke(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        $iRequest = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username' => $request->get('email'),
            'password' => $request->get('password'),
        ]);

        $response = app()->handle($iRequest, HttpKernelInterface::SUB_REQUEST);

        if ($response->getStatusCode() != 200) {
            return response()->json([
                'message' => 'Invalid email or password',
                'status' => 422
            ], 422);
        }

        $data = json_decode($response->getContent());

        return response()->json([
            'token' => $data->access_token,
        ], 200, [
            'Authorization' => 'Bearer '. $data->access_token
        ]);
    }

    /**
     * Get the login validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'email' => 'required',
            'password' => 'required|min:6',
        ];
    }

    /**
     * Get the login validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }
}
