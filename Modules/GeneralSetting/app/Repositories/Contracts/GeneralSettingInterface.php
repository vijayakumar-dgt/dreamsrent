<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface GeneralSettingInterface
{
    public function retrieval(): SettingsRetrievalInterface;

    public function management(): SettingsManagementInterface;

    public function security(): UserSecuritySettingsInterface;

    public function payment(): PaymentAndStorageSettingsInterface;
}
