<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationSettingsController extends BaseUserController
{
    public function index(): View
    {
        $data = $this->userRepository->getUserNotifications();

        return view('frontend.user.notification', $data);
    }

    public function update(Request $request): JsonResponse
    {
        $response = $this->userRepository->updateNotificationSettings($request);

        return response()->json($response, $response['code'] ?? 200);
    }
}
