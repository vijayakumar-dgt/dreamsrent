<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterRequest;
use App\Http\Requests\SendNewsLetterRequest;
use App\Repositories\Contracts\NewsLetterRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    protected NewsLetterRepositoryInterface $newsLetterRepository;

    public function __construct(NewsLetterRepositoryInterface $newsLetterRepository)
    {
        $this->newsLetterRepository = $newsLetterRepository;
    }

    public function index(): View
    {
        return view('admin.newsletters');
    }

    public function store(NewsletterRequest $request): JsonResponse
    {
        $result = $this->newsLetterRepository->save($request);
        return response()->json($result, $result['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $result = $this->newsLetterRepository->list($request);
        return response()->json($result, $result['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $result = $this->newsLetterRepository->delete($request);
        return response()->json($result, $result['code']);
    }

    public function sendNewsletter(SendNewsLetterRequest $request): JsonResponse
    {
        $result = $this->newsLetterRepository->sendNewsletter($request);
        return response()->json($result, $result['code']);
    }
}
