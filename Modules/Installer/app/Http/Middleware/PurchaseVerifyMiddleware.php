<?php

namespace Modules\Installer\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Modules\Installer\Enums\InstallerInfo;
use Modules\Installer\Models\Configuration;

class PurchaseVerifyMiddleware
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
        $appMode  = config('app.app_mode');
        $response = $this->invalidHashed();

        if (is_string($appMode) && strtolower($appMode) === 'demo') {
            $response = $next($request);
        } elseif (InstallerInfo::licenseFileExist()) {
            $filepath = InstallerInfo::getLicenseFilePath();

            if (!InstallerInfo::isRemoteLocal() && InstallerInfo::licenseFileDataHasLocalTrue()) {
                $verification = purchaseVerificationHashed($filepath, true);
                if (InstallerInfo::rewriteHashedFile($verification)) {
                    $response = $next($request);
                } else {
                    InstallerInfo::deleteLicenseFile();
                    $response = $this->invalidHashed();
                }
            } elseif (Carbon::now()->day == 1) {
                $verification = purchaseVerificationHashed($filepath);
                if ($verification['success']) {
                    $response = $next($request);
                } else {
                    $response = $this->invalidHashed();
                }
            } else {
                $response = $next($request);
            }
        }

        return $response;
    }

    /**
     * Handle invalid hashed file scenario.
     *
     * @return RedirectResponse
     */
    private function invalidHashed(): RedirectResponse
    {
        try {
            Configuration::updateCompeteStatus(0);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
        Session::flush();
        Artisan::call('cache:clear');

        return redirect()->route('setup.verify');
    }
}
