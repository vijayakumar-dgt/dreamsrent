<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\UpdateUserPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecurityController extends BaseUserController
{
    public function index(): View
    {
        $seoTitle = __('web.user.security');

        return view('frontend.user.security', ['seo_title' => $seoTitle]);
    }

    public function checkCurrentPassword(Request $request): JsonResponse
    {
        $response = $this->securityRepository->checkCurrentPassword($request);

        return response()->json($response, $response['code'] ?? 200);
    }

    public function updatePassword(UpdateUserPasswordRequest $request): JsonResponse
    {
        $response = $this->securityRepository->updatePassword($request);

        return response()->json($response, $response['code'] ?? 200);
    }

    public function details(): JsonResponse
    {
        $response = $this->securityRepository->getSecuritySettings();

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $response,
        ]);
    }

    public function logoutDevice(Request $request): JsonResponse
    {
        $response = $this->securityRepository->logoutDevice($request);

        return response()->json($response, $response['code'] ?? 200);
    }
}
