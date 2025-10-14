<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;

class DashboardController extends BaseUserController
{
    public function index(): View
    {
        $data = $this->userRepository->getDashboardData();

        return view('frontend.user.dashboard', $data);
    }
}
