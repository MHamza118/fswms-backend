<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class IsSalesman
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Assuming the user has a 'role' column in the database
        if (auth()->check() && auth()->user()->role === 'salesman') {
            return $next($request); // Proceed if the user is a salesman user
        }

        // Return unauthorized response if not a salesman user
        return errorResponse("You are not a salesman.", [], 403);
    }

}
