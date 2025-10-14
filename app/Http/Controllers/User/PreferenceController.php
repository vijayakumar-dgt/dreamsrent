<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreferenceController extends BaseUserController
{
    public function index(): View
    {
        $data = $this->userRepository->getPreferenceSettings();

        return view('frontend.user.preference', $data);
    }

    public function update(Request $request): JsonResponse
    {
        $response = $this->userRepository->updatePreference($request);

        return response()->json($response, $response['code'] ?? 200);
    }

    public function get(Request $request): JsonResponse
    {
        $response = $this->userRepository->getPreferences($request);

        return response()->json($response, $response['code'] ?? 200);
    }
}
