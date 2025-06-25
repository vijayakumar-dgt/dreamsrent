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

    public function BlogList(Request $request): View|JsonResponse
    {
        return $this->blogRepository->BlogList($request);
    }

    public function BlogDetail(int|string $id): View
    {
        $data = $this->blogRepository->BlogDetail($id);
        return view('frontend.blogs.blog-details', [...$data]);
    }

    public function storeReview(BlogRequest $request)
    {
        return $this->blogRepository->storeReview($request);
    }
}
