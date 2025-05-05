<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Installer\app\Enums\InstallerInfo;
use Modules\Installer\app\Models\Configuration;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\EmailTemplate;
use stdClass;

if (!function_exists('setupStatus')) {
    /**
     * Check if the setup process has been completed.
     *
     * @return bool
     */
    function setupStatus(): bool
    {
        $cacheKey = 'setup_complete_status';

        if (!Cache::has($cacheKey)) {
            try {
                Cache::rememberForever($cacheKey, function () {
                    return Configuration::where('config', 'setup_complete')->first()?->value != 0;
                });
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                Cache::rememberForever($cacheKey, fn() => false);
            }
        }

        return Cache::get($cacheKey, false);
    }
}

/**
 * Verify the license file by sending hashed data to a remote server.
 *
 * @param string $filepath
 * @param bool $isLocal
 * @return array{success: bool, message: string}
 */
function purchaseVerificationHashed(string $filepath, bool $isLocal = false): array
{
    // Skip verification in demo mode
    if (strtolower(config('app.app_mode')) === 'demo') {
        return ['success' => true, 'message' => 'Demo mode - verification bypassed'];
    }

    // Proceed only if the license file exists
    if (file_exists($filepath)) {
        $licenseFile = \Modules\Installer\app\Enums\InstallerInfo::getLicenseFileData();

        $data = [];

        if ($isLocal) {
            $data['isLocal'] = \Modules\Installer\app\Enums\InstallerInfo::licenseFileDataHasLocalTrue() ? 'false' : 'true';
            $data['purchase_code'] = $licenseFile['purchase_code'];
        }

        $data['verification_hashed'] = $licenseFile['verification_hashed'];
        $data['incoming_url'] = \Modules\Installer\app\Enums\InstallerInfo::getHost();
        $data['incoming_ip'] = \Modules\Installer\app\Enums\InstallerInfo::getRemoteAddr();

        return Http::post(
            \Modules\Installer\app\Enums\InstallerInfo::VERIFICATION_HASHED_URL->value,
            $data
        )->json();
    }

    // Treat missing file as demo
    return ['success' => true, 'message' => 'Demo mode - verification bypassed'];
}

if (! function_exists('changeEnvValues')) {
    /**
     * Safely update a key-value pair in the .env file.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    function changeEnvValues(string $key, string $value): void
    {
        $envPath = app()->environmentFilePath();
        $envContent = file_get_contents($envPath);

        if ($envContent === false) {
            // Log or handle the error if needed
            return;
        }

        // Use a regular expression to safely replace the value
        $envContent = preg_replace(
            "/^{$key}=.*/m",
            "{$key}={$value}",
            $envContent
        );

        if ($envContent !== null) {
            file_put_contents($envPath, $envContent);
        }
    }
}


if (! function_exists('updateChecking')) {
    /**
     * Check for available updates from the remote update server.
     *
     * @param string $last_update_date
     * @return string|false
     */
    function updateChecking(string $last_update_date): string|false
    {
        $cacheKey = 'update_url';

        if (! Cache::has($cacheKey)) {
            try {
                Cache::remember($cacheKey, now()->addDay(), function () use ($last_update_date) {
                    $response = Http::post(InstallerInfo::UPDATE_CHECK_URL->value, [
                        'updated_at' => $last_update_date,
                        'verification_hashed' => InstallerInfo::getLicenseFileData()['verification_hashed'],
                    ])->json();

                    if (isset($response['success']) && $response['success']) {
                        return $response['update_url'];
                    }

                    return false;
                });
            } catch (Exception $e) {
                Cache::remember($cacheKey, now()->addDay(), fn () => false);
                Log::error($e->getMessage());
            }
        }

        return Cache::get($cacheKey);
    }
}

if (! function_exists('showUpdateAvailablity')) {
    function showUpdateAvailablity(): stdClass
    {
        if (Cache::has('setting') && $settings = Cache::get('setting')) {
            if ($settings->last_update_date && $update_url = updateChecking($settings->last_update_date)) {
                return (object) [
                    'status' => true,
                    'message' => __('Update is available'),
                    'url' => $update_url,
                ];
            }
        }

        return (object) [
            'status' => false,
            'message' => __('Your are using latest version already.'),
            'url' => null,
        ];
    }
}

if (!function_exists('getTemplatedEmailContent')) {
    /**
     * Get raw subject and content from a template.
     *
     * @param string $notificationType
     * @return array{subject: string, content: string}|null
     */
    function getTemplatedEmailContent(string $notificationType): ?array
    {
        $template = EmailTemplate::select('subject', 'description')
            ->where('notification_type', $notificationType)
            ->first();

        if (!$template) {
            return null;
        }

        return [
            'subject' => $template->subject,
            'content' => $template->description,
        ];
    }
}
