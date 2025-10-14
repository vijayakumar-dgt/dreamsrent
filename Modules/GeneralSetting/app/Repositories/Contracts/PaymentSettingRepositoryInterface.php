<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface PaymentSettingRepositoryInterface
{
    public function updatePaymentSettings(array $data): bool;

    public function updatePaymentStatus(array $data): bool;

    public function getPaymentSettings(int $groupId, string $orderBy = 'desc'): array;

    public function updateEnvVariables(array $envData): bool;

    public function updateStorageStatus(string $storageType, bool $status): bool;

    public function updateAwsSettings(array $settings): bool;

    public function updateOrCreateStorageSetting(array $conditions, array $data): bool;
}
