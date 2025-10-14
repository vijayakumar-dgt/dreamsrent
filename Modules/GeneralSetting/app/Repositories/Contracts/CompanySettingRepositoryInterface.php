<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface CompanySettingRepositoryInterface
{
    public function getSettingsByGroup(int $groupId);

    public function storeCompanySettings(array $data): void;

    public function getCompanySettings(int $groupId): ?array;

    public function saveNotificationSettings(array $data): void;

    public function updatePrefixes(array $settings, int $groupId): void;

    public function getPrefixesByGroup(int $groupId);
}
