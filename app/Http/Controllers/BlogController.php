<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogRequest;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    protected BlogRepositoryInterface $blogRepository;

    public function __construct(BlogRepositoryInterface $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    public function blogList(Request $request): View|JsonResponse
    {
        return $this->blogRepository->blogList($request);
    }

    public function blogDetail(int|string $id): View
    {
        $data = $this->blogRepository->blogDetail($id);
        return view('frontend.blogs.blog-details', [...$data]);
    }

    public function storeReview(BlogRequest $request)
    {
        return $this->blogRepository->storeReview($request);
    }
}
