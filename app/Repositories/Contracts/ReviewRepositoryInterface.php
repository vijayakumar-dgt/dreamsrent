<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface ReviewRepositoryInterface
{
    public function addReview(Request $request);
    public function addReply(Request $request);
    public function getReviewsList(Request $request);
    public function fetchReviewReplies(int $reviewId);
    public function getUserReviewsList(Request $request);
    public function delete(int $id);
    public function adminReviewsList(Request $request);
}
