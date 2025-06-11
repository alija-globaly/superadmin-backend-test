<?php

namespace Agentcis\Auth\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!$request->user()->hasAnyRole($roles)) {
            throw new AccessDeniedHttpException('Oops, Look like you don\'t have permission to perform this action');
        }

        return $next($request);
    }
}
