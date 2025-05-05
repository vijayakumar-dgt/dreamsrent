<?php

namespace Modules\Installer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Response;

class SetupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (empty(config('app.key'))) {
            Artisan::call('key:generate');
            Artisan::call('config:cache');
        }

        $setupStatus = setupStatus();

        if ($request->is('setup/*')) {
            if ($setupStatus) {
                return redirect()->route('home');
            }

            return $next($request);
        }

        if (! $setupStatus) {
            return redirect()->route('setup.verify');
        }

        return $next($request);
    }
}
