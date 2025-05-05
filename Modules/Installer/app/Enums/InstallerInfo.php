<?php

namespace Modules\Installer\Enums;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

enum InstallerInfo: string
{
    // Constants for database paths
    const DUMMY_DATABASE_PATH = 'database/backup/rental-system.sql';
    const FRESH_DATABASE_PATH = 'database/backup/rental-system.sql';

    // Enum cases
    case LICENSE_FILE_PATH = 'app/license.json';
    case VERIFICATION_URL = 'https://envato.dreamstechnologies.com/verify/verify.php';
    case VERIFICATION_HASHED_URL = 'https://pcv.dreamstechnologies.com/api/v1/validate/code';
    case UPDATE_CHECK_URL = 'https://pcv.dreamstechnologies.com/api/v1/updateable';
    case ITEM_ID = '53608520';

    public static function getDummyDatabaseFilePath(): string
    {
        return base_path(self::DUMMY_DATABASE_PATH);
    }

    public static function getFreshDatabaseFilePath(): string
    {
        return base_path(self::FRESH_DATABASE_PATH);
    }

    public static function getLicenseFilePath(): string
    {
        return storage_path(self::LICENSE_FILE_PATH->value);
    }

    public static function getAllLocalIp(): array
    {
        return [
            'localhost',
            '127.0.0.1',
            '::1',
            '0:0:0:0:0:0:0:1',
            '::ffff:127.0.0.1',
            '0:0:0:0:0:0:127.0.0.1',
            '0.0.0.0',
        ];
    }

    public static function isLocal(string $value): bool
    {
        return in_array($value, self::getAllLocalIp(), true);
    }

    public static function isRemoteLocal(): bool
    {
        return self::isLocal(self::getRemoteAddr());
    }

    public static function getHost(): string
    {
        $urlComponents = parse_url(request()->root());
        return $urlComponents['host'] ?? '';
    }

    public static function getRemoteAddr(): string
    {
        return request()->server('REMOTE_ADDR') ?? '';
    }

    public static function licenseFileExist(): bool
    {
        return File::exists(self::getLicenseFilePath());
    }

    public static function hasLocalInLicense(): bool
    {
        return self::isLocal(self::getHost());
    }

    public static function getLicenseFileData(bool $isJson = true): mixed
    {
        if (self::licenseFileExist()) {
            $fileContent = file_get_contents(self::getLicenseFilePath());

            if ($fileContent === false) {
                Log::error('Failed to read the license file.');
                return null;
            }

            if ($isJson) {
                return json_decode($fileContent, true);
            }

            return $fileContent;
        }

        return null;
    }

    public static function licenseFileDataHasLocalTrue(): bool
    {
        $data = self::getLicenseFileData();
        if ($data !== null) {
            return isset($data['isLocal']) && $data['isLocal'] === true;
        }

        return false;
    }

    public static function deleteLicenseFile(): void
    {
        if (self::licenseFileExist()) {
            File::delete(self::getLicenseFilePath());
        }
    }

    /**
     * Rewrite license hash data to the file based on verification response.
     *
     * @param array{
     *     success: bool,
     *     isLocal?: string,
     *     newHash?: string,
     *     verification_hashed?: string,
     *     last_updated_at?: string|null
     * } $response
     */
    public static function rewriteHashedFile(array $response, ?string $purchaseCode = null): bool
    {
        if (!empty($response['last_updated_at'])) {
            Cache::put('last_updated_at', $response['last_updated_at']);
        }

        $isLocal = filter_var($response['isLocal'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        $data = [];

        if (
            isset($response['success']) &&
            $response['success'] &&
            $isLocal === false
        ) {
            $data['verification_hashed'] = $response['newHash'] ?? '';
        } elseif ($response['success']) {
            if ($purchaseCode !== null && self::isRemoteLocal()) {
                $data['isLocal'] = true;
                $data['purchase_code'] = $purchaseCode;
            }
            $data['verification_hashed'] = $response['verification_hashed'] ?? '';
        } else {
            return false;
        }

        try {
            $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            if ($encoded === false) {
                throw new \RuntimeException('Failed to encode license data.');
            }

            file_put_contents(self::getLicenseFilePath(), $encoded);
            return true;
        } catch (\Throwable $e) {
            Log::error('Error rewriting hashed file: ' . $e->getMessage());
            return false;
        }
    }


    public static function writeAssetUrl(): bool
    {
        try {
            $plainUrl = url('/');

            $assetUrl = self::isRemoteLocal() ? $plainUrl : url('/public');

            if (config('app.asset_url') !== $assetUrl) {
                self::changeEnvValues('ASSET_URL', $assetUrl);
            }

            if (config('app.url') !== $plainUrl) {
                self::changeEnvValues('APP_URL', $plainUrl);
            }

            return true;
        } catch (Exception $ex) {
            Log::error('Error writing asset URL: ' . $ex->getMessage());
            return false;
        }
    }

    public static function changeEnvValues(string $key, string $value): bool
    {
        try {
            $envFile = base_path('.env');
            if (File::exists($envFile)) {
                $envContent = File::get($envFile) ?? '';

                $pattern = "/^{$key}=.*/m";
                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, "{$key}={$value}", $envContent) ?? $envContent;
                } else {
                    $envContent .= "\n{$key}={$value}";
                }

                File::put($envFile, $envContent);
                return true;
            }
        } catch (Exception $e) {
            Log::error('Error changing env values: ' . $e->getMessage());
        }

        return false;
    }


    /**
     * Validates the provided purchase code against the local license file.
     *
     * @param string $purchaseCode
     * @return array{success: bool, message: string}
     */
    public static function localValidatePurchase(string $purchaseCode): array
    {
        $licenseData = self::getLicenseFileData();

        if ($licenseData === null) {
            return [
                'success' => false,
                'message' => 'License file does not exist.',
            ];
        }

        if (!isset($licenseData['purchase_code']) || $licenseData['purchase_code'] !== $purchaseCode) {
            return [
                'success' => false,
                'message' => 'Invalid purchase code.',
            ];
        }

        if (isset($licenseData['isLocal']) && $licenseData['isLocal'] === false) {
            return [
                'success' => false,
                'message' => 'License is not marked as local.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Purchase code validated successfully.',
        ];
    }
}
