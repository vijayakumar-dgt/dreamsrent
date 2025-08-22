<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserLoginRegisterInterface
{
    public function resetPasswordUpdate(Request $request);

    public function getOtpSettings(Request $request);

    public function verifyOtp(Request $request);

    public function validateEmail(string $email);

    public function register(Request $request);

    public function login(Request $request);
}
