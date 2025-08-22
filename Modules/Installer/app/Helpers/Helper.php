<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\GeneralSetting\Models\EmailTemplate;
use Modules\Installer\Enums\InstallerInfo;
use Modules\Installer\Models\Configuration;
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
                Cache::rememberForever($cacheKey, function (): bool {
                    $config = Configuration::where('config', 'setup_complete')->first();
                    return $config && $config->value != 0;
                });
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                Cache::rememberForever($cacheKey, fn (): bool => false);
            }
        }

        $result = Cache::get($cacheKey, false);
        return is_bool($result) ? $result : false;
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
    $appMode = config('app.app_mode');
    if (is_string($appMode) && strtolower($appMode) === 'demo') {
        return ['success' => true, 'message' => 'Demo mode - verification bypassed'];
    }

    // Proceed only if the license file exists
    if (file_exists($filepath)) {
        $licenseFile = InstallerInfo::getLicenseFileData();

        if (!is_array($licenseFile)) {
            return ['success' => false, 'message' => 'Invalid license file format'];
        }

        $data = [];

        if ($isLocal) {
            $data['isLocal'] = InstallerInfo::licenseFileDataHasLocalTrue() ? 'false' : 'true';
            if (isset($licenseFile['purchase_code']) && is_string($licenseFile['purchase_code'])) {
                $data['purchase_code'] = $licenseFile['purchase_code'];
            }
        }

        if (isset($licenseFile['verification_hashed']) && is_string($licenseFile['verification_hashed'])) {
            $data['verification_hashed'] = $licenseFile['verification_hashed'];
        }

        $data['incoming_url'] = InstallerInfo::getHost();
        $data['incoming_ip'] = InstallerInfo::getRemoteAddr();

        $response = Http::post(
            InstallerInfo::VERIFICATION_HASHED_URL->value,
            $data
        )->json();

        // Strict type validation of the response
        if (
            !is_array($response)
            || !array_key_exists('success', $response)
            || !is_bool($response['success'])
            || !array_key_exists('message', $response)
            || !is_string($response['message'])
        ) {
            return ['success' => false, 'message' => 'Invalid verification response'];
        }

        /** @var array{success: bool, message: string} $response */
        return $response;
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
     * @return string|false Returns update URL string or false
     */
    function updateChecking(string $last_update_date): string|false
    {
        $cacheKey = 'update_url';

        if (!Cache::has($cacheKey)) {
            try {
                $licenseData = InstallerInfo::getLicenseFileData();
                $verificationHashed = is_array($licenseData) && isset($licenseData['verification_hashed'])
                    ? $licenseData['verification_hashed']
                    : '';

                $response = Http::post(InstallerInfo::UPDATE_CHECK_URL->value, [
                    'updated_at'          => $last_update_date,
                    'verification_hashed' => $verificationHashed,
                ])->json();

                // Validate response structure
                if (is_array($response) && isset($response['success']) && $response['success'] === true) {
                    $updateUrl = $response['update_url'] ?? false;
                    $finalUrl = is_string($updateUrl) ? $updateUrl : false;

                    Cache::put($cacheKey, $finalUrl, now()->addDay());

                    return $finalUrl;
                }

                Cache::put($cacheKey, false, now()->addDay());
                return false;
            } catch (Exception $e) {
                Log::error($e->getMessage());
                Cache::put($cacheKey, false, now()->addDay());
                return false;
            }
        }

        $cachedValue = Cache::get($cacheKey);
        return is_string($cachedValue) ? $cachedValue : false;
    }
}

if (! function_exists('showUpdateAvailablity')) {
    function showUpdateAvailablity(): stdClass
    {
        if (Cache::has('setting')) {
            $settings = Cache::get('setting');

            if (is_object($settings) && isset($settings->last_update_date)) {
                $update_url = updateChecking($settings->last_update_date);

                if ($update_url) {
                    return (object) [
                        'status'  => true,
                        'message' => __('Update is available'),
                        'url'     => $update_url,
                    ];
                }
            }
        }

        return (object) [
            'status'  => false,
            'message' => __('You are using the latest version already.'),
            'url'     => null,
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

        if (!$template || is_null($template->subject) || is_null($template->description)) {
            return null;
        }

        return [
            'subject' => (string)$template->subject,
            'content' => (string)$template->description,
        ];
    }
}
