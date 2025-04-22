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
        //check if user logged in and user_type is admin
        if(!Auth::guard('admin')->check()){
            return to_route('admin-login');
        }elseif(Auth::guard('admin')->check() && ((Auth::guard('admin')->user()->user_type !== 0) && (Auth::guard('admin')->user()->user_type !== 1) && (Auth::guard('admin')->user()->user_type !== 2))){
            abort(403);
        }
        Auth::shouldUse('admin');

        return $next($request);
    }
}
