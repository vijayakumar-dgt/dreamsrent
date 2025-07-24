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
    protected NewsLetterRepositoryInterface $NewsLetterRepository;

    public function __construct(NewsLetterRepositoryInterface $NewsLetterRepository)
    {
        $this->NewsLetterRepository = $NewsLetterRepository;
    }

    public function index(Request $request): View
    {
        return view('admin.newsletters');
    }

    public function store(NewsletterRequest $request): JsonResponse
    {
        $result = $this->NewsLetterRepository->save($request);
        return response()->json($result, $result['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $result = $this->NewsLetterRepository->list($request);
        return response()->json($result, $result['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $result = $this->NewsLetterRepository->delete($request);
        return response()->json($result, $result['code']);
    }

    public function sendNewsletter(SendNewsLetterRequest $request): JsonResponse
    {
        $result = $this->NewsLetterRepository->sendNewsletter($request);
        return response()->json($result, $result['code']);
    }
}
