<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface UserSecuritySettingsInterface
{
    public function updatePassword(array $data);

    public function updatePhoneNumber(array $data);

    public function updateEmail(array $data);

    public function logoutDevice(array $data);
}
