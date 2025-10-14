<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface AppearanceSettingRepositoryInterface
{
    public function storeSeoSettings(array $data, ?int $groupId = 6): void;

    public function storeLogoSettings(array $files, int $groupId = 16): array;

    public function storeMaintenanceSettings(array $data): void;

    public function updateThemeSettings(array $data): void;
}
