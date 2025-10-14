<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface GeneralSettingInterface extends
    SettingsRetrievalInterface,
    SettingsManagementInterface,
    UserSecuritySettingsInterface,
    PaymentAndStorageSettingsInterface
{
}
