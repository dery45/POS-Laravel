<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class TokenExpiredRedirect
{
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->tokenExpired()) {
            // Token expired, redirect to login
            throw new AuthenticationException('Unauthenticated.');
        }

        return $next($request);
    }
}
