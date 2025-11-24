<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->verified) {
            return errorResponse("Your account is not verified.", [], 403);
        }

        return $next($request);
    }
}
