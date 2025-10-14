<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;

class IntegrationController extends BaseUserController
{
    public function index(): View
    {
        $data = $this->profileRepository->getProfileSettings();

        return view('frontend.user.usersettings', $data);
    }
}
