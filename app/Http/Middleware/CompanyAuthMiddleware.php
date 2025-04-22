<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('company')->check()) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized (company)'], 401);
    }
}
