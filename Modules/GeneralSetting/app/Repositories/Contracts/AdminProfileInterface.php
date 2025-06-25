<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface AdminProfileInterface
{
    public function getProfile(): array;

    public function updateProfile(array $data): array;

    public function checkPassword(string $currentPassword): array;

    public function deleteAccount(): array;
}
