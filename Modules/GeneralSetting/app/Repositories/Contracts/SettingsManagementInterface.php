<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface SettingsManagementInterface
{
    public function storeCompanySettings(array $data): void;

    public function saveNotificationSettings(array $data): void;

    public function updatePrefixes(array $settings, int $groupId);

    public function storeSeoSettings(array $data, ?int $groupId = 6): void;

    public function storeLogoSettings(array $files, int $groupId = 16): array;

    public function storeMaintenanceSettings(array $data): void;

    public function updateThemeSettings(array $data): void;

    public function storeOtpSettings(array $data): void;

    public function updateCopyright(array $data): void;

    public function saveRentalSettings(array $data);

    public function saveInvoiceSettings(array $data);

    public function storeCookiesSettings(array $data);

    public function storeHowItWorks(array $data): void;
}
