<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Repositories\Contracts\PaymentAndStorageSettingsInterface;

class PaymentAndStorageSettingsRepository implements PaymentAndStorageSettingsInterface
{
    public function updatePaymentSettings(array $data): bool
    {
        $groupId = $data['group_id'];
        $envUpdates = [];

        foreach ($data as $key => $value) {
            if ($key === 'group_id' || $key === '_token') {
                continue;
            }

            $this->updateOrCreateSettingPayment(
                ['key' => $key, 'group_id' => $groupId],
                ['value' => $value]
            );

            $envUpdates += $this->mapPaymentKeyToEnv($key, $value);
        }

        if (!empty($envUpdates)) {
            $this->updateEnvVariables($envUpdates);
        }

        return true;
    }

    public function updatePaymentStatus(array $data): bool
    {
        return $this->updateOrCreateSettingPayment(
            ['key' => $data['key'], 'group_id' => $data['group_id']],
            ['value' => $data['value']]
        );
    }

    public function updateEnvVariables(array $envData): bool
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            return false;
        }

        $envContent = file_get_contents($path);
        if ($envContent === false) {
            return false;
        }

        foreach ($envData as $key => $value) {
            $pattern = "/^{$key}=.*/m";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        return file_put_contents($path, $envContent) !== false;
    }

    public function updateStorageStatus(string $storageType, bool $status): bool
    {
        $oppositeStorageType = $storageType === 'local_storage' ? 'aws_storage' : 'local_storage';
        $oppositeStatus = !$status;

        $this->updateOrCreateStorageSetting(
            ['key' => $storageType],
            ['value' => $status, 'group_id' => 8]
        );

        $this->updateOrCreateStorageSetting(
            ['key' => $oppositeStorageType],
            ['value' => $oppositeStatus, 'group_id' => 8]
        );

        return true;
    }

    public function updateAwsSettings(array $settings): bool
    {
        foreach ($settings as $key => $value) {
            $this->updateOrCreateStorageSetting(
                ['key' => $key],
                ['value' => $value, 'group_id' => 8]
            );
        }

        return true;
    }

    public function updateOrCreateStorageSetting(array $conditions, array $data): bool
    {
        return (bool) GeneralSetting::updateOrCreate($conditions, $data);
    }

    protected function updateOrCreateSettingPayment(array $conditions, array $data): bool
    {
        return (bool) GeneralSetting::updateOrCreate($conditions, $data);
    }

    protected function mapPaymentKeyToEnv(string $key, mixed $value): array
    {
        return match ($key) {
            'paypal_key'    => ['PAYPAL_SANDBOX_CLIENT_ID' => $value],
            'paypal_secret' => ['PAYPAL_SANDBOX_CLIENT_SECRET' => $value],
            'stripe_key'    => ['STRIPE_KEY' => $value],
            'stripe_secret' => ['STRIPE_SECRET' => $value],
            default         => [],
        };
    }
}
