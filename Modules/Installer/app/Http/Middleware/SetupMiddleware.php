<?php

namespace Modules\Installer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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
        $response    = $next($request);

        if ($request->is('setup/*')) {
            if ($setupStatus) {
                $response = redirect()->route('home');
            } else {
                $response = $next($request);
            }
        } elseif (! $setupStatus) {
            $response = redirect()->route('setup.verify');
        }

        return $response;
    }
}
