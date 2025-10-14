<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;

class IntegrationController extends BaseUserController
{
    public function index(): View
    {
        $data = $this->userRepository->getProfileSettings();

        return view('frontend.user.usersettings', $data);
    }
}
