<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\GeneralSetting\Models\GeneralSetting;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin/*') || $request->is('maintenance/*')) {
            return $next($request);
        }
        $isMaintenance = GeneralSetting::where('group_id', 4)->where('key', 'maintenance_status')->pluck('value')->first();

        if ($isMaintenance == 1) {
            return redirect()->route('maintenance');
        }
        return $next($request);
    }
}
