<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return to_route('admin-login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::guard('admin')->user();

        if (!in_array($user->user_type, [1, 2], true)) {
            abort(403);
        }

        Auth::shouldUse('admin');

        return $next($request);
    }
}
