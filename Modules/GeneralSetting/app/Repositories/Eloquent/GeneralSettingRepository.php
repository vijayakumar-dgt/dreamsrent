<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Modules\GeneralSetting\Repositories\Contracts\GeneralSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\PaymentAndStorageSettingsInterface;
use Modules\GeneralSetting\Repositories\Contracts\SettingsManagementInterface;
use Modules\GeneralSetting\Repositories\Contracts\SettingsRetrievalInterface;
use Modules\GeneralSetting\Repositories\Contracts\UserSecuritySettingsInterface;

class GeneralSettingRepository implements GeneralSettingInterface
{
    public function __construct(
        protected SettingsRetrievalInterface $settingsRetrieval,
        protected SettingsManagementInterface $settingsManagement,
        protected UserSecuritySettingsInterface $userSecuritySettings,
        protected PaymentAndStorageSettingsInterface $paymentAndStorageSettings
    ) {
    }

    public function retrieval(): SettingsRetrievalInterface
    {
        return $this->settingsRetrieval;
    }

    public function management(): SettingsManagementInterface
    {
        return $this->settingsManagement;
    }

    public function security(): UserSecuritySettingsInterface
    {
        return $this->userSecuritySettings;
    }

    public function payment(): PaymentAndStorageSettingsInterface
    {
        return $this->paymentAndStorageSettings;
    }
}
