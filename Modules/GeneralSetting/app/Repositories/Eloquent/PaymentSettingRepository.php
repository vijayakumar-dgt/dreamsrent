<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Repositories\Contracts\PaymentSettingRepositoryInterface;

class PaymentSettingRepository implements PaymentSettingRepositoryInterface
{
    public function updatePaymentSettings(array $data): bool
    {
        $groupId = $data['group_id'];
        $envUpdates = [];

        foreach ($data as $key => $value) {
            if ($key !== 'group_id' && $key !== '_token') {
                $this->updateOrCreateSettingPayment(
                    ['key' => $key, 'group_id' => $groupId],
                    ['value' => $value]
                );

                switch ($key) {
                    case 'paypal_key':
                        $envUpdates['PAYPAL_SANDBOX_CLIENT_ID'] = $value;
                        break;
                    case 'paypal_secret':
                        $envUpdates['PAYPAL_SANDBOX_CLIENT_SECRET'] = $value;
                        break;
                    case 'stripe_key':
                        $envUpdates['STRIPE_KEY'] = $value;
                        break;
                    case 'stripe_secret':
                        $envUpdates['STRIPE_SECRET'] = $value;
                        break;
                    default:
                        break;
                }
            }
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

    public function getPaymentSettings(int $groupId, string $orderBy = 'desc'): array
    {
        return GeneralSetting::where('group_id', $groupId)
            ->orderBy('id', $orderBy)
            ->get()
            ->toArray();
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

    private function updateOrCreateSettingPayment(array $conditions, array $data): bool
    {
        return GeneralSetting::updateOrCreate($conditions, $data) ? true : false;
    }
}
