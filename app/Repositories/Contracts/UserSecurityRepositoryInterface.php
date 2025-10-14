<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserSecurityRepositoryInterface
{
    public function checkCurrentPassword(Request $request);

    public function updatePassword(Request $request);

    public function getSecuritySettings();

    public function logoutDevice(Request $request);

    public function deleteAccount();
}
