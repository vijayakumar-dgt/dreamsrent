<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserSecurityRepositoryInterface
{
    public function checkCurrentPassword(Request $request): array;

    public function updatePassword(Request $request): array;

    public function getSecuritySettings(): array;

    public function logoutDevice(Request $request): array;

    public function deleteAccount(): array;
}
