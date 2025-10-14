<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnquiryController extends BaseUserController
{
    public function store(Request $request): JsonResponse
    {
        $response = $this->profileRepository->storeEnquiry($request);

        return response()->json($response, $response['code'] ?? 200);
    }
}
