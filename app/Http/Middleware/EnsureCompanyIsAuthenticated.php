<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureCompanyIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        Auth::shouldUse('company');

        if (!Auth::guard('company')->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}
