<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class ReviewController extends Controller
{
    protected ?Authenticatable $authUser;
    protected ReviewRepositoryInterface $reviewRepository;
    public function __construct(ReviewRepositoryInterface $reviewRepository)
    {
        $this->authUser = current_user();
        $this->reviewRepository = $reviewRepository;
    }

    public function addreview(Request $request): JsonResponse
    {
        
        $response = $this->reviewRepository->addReview($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function addReply(Request $request): JsonResponse
    {
        $response = $this->reviewRepository->addReply($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function reviewsList(Request $request): JsonResponse
    {
        $response = $this->reviewRepository->getReviewsList($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    /**
     * Fetch review replies.
     *
     * @param int|null $reviewId
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReviewMessages>
     */
    function fetchReviewReplies(?int $reviewId): Collection
    {
        $replies = $this->reviewRepository->fetchReviewReplies($reviewId);
        return $replies;
    }

    public function userReviewsList(Request $request): JsonResponse
    {
        $response = $this->reviewRepository->getUserReviewsList($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function delete(Request $request): JsonResponse
    {
        $response = $this->reviewRepository->delete((int) $request->id);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function adminReviews(Request $request): View
    {
        return view('admin.reviews');
    }

    public function adminReviewsList(Request $request): JsonResponse
    {
        $response = $this->reviewRepository->adminReviewsList($request);
        return response()->json($response, $response['code'] ?? 200);
    }
}
