<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\UserProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProfileController extends BaseUserController
{
    public function settings(): View
    {
        $data = $this->profileRepository->getProfileSettings();

        return view('frontend.user.usersettings', $data);
    }

    public function update(UserProfileRequest $request): JsonResponse
    {
        $response = $this->profileRepository->updateProfile($request);

        return response()->json($response, $response['code'] ?? 200);
    }
}
