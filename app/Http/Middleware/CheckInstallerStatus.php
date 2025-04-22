<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use Symfony\Component\HttpFoundation\Response;

class CheckInstallerStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $modulesStatusPath = base_path('modules_statuses.json');

        if (File::exists($modulesStatusPath)) {
            $modulesStatus = json_decode(File::get($modulesStatusPath), true);

            if (isset($modulesStatus['Installer']) && $modulesStatus['Installer'] === true) {
                return redirect()->route('setup.verify');
            }
        }

        return $next($request);
    }

}
