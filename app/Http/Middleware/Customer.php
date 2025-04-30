<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Customer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check()) {
            return to_route('user-login');
        } elseif (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type !== 3) {
            abort(403);
        }
        Auth::shouldUse('web');
        return $next($request);
    }
}
