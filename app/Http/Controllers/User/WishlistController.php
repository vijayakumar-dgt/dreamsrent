<?php

namespace App\Http\Controllers\User;

use App\Http\Resources\UserWishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends BaseUserController
{
    public function index(): View
    {
        $seoTitle = $this->userRepository->getWishlistData();

        return view('frontend.user.wishlists', ['seo_title' => $seoTitle]);
    }

    public function store(Request $request): JsonResponse
    {
        $response = $this->userRepository->addToWishlist($request->id);

        return response()->json($response, $response['code'] ?? 200);
    }

    public function list(): JsonResponse
    {
        $wishlists = $this->userRepository->getWishlistDataAjax();

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => UserWishlist::collection($wishlists),
        ]);
    }
}
