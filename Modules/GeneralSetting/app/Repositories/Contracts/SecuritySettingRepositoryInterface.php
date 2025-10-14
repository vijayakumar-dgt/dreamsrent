<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface SecuritySettingRepositoryInterface
{
    public function storeOtpSettings(array $data): void;

    public function updatePassword(array $data);

    public function updatePhoneNumber(array $data);

    public function updateEmail(array $data);

    public function getSecuritySettings();

    public function logoutDevice(array $data);
}
