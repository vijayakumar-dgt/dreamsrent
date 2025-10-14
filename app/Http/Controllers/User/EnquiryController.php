<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnquiryController extends BaseUserController
{
    public function store(Request $request): JsonResponse
    {
        $response = $this->userRepository->storeEnquiry($request);

        return response()->json($response, $response['code'] ?? 200);
    }
}
