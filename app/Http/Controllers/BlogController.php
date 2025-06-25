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
        $data = $this->blogRepository->BlogList($request);
        return $data;
    }

    public function BlogDetail(int|string $id): View
    {
        $data = $this->blogRepository->BlogDetail($id);
        return view('frontend.blogs.blog-details', [...$data]);
    }

    public function storeReview(BlogRequest $request)
    {
        $data = $this->blogRepository->storeReview($request);
        return $data;
    }
}
