<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\JsonResponse;

class AccountController extends BaseUserController
{
    public function destroy(): JsonResponse
    {
        $response = $this->userRepository->deleteAccount();

        return response()->json($response, $response['code'] ?? 200);
    }
}
