<?php

namespace Modules\Communication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Communication\Http\Requests\AnnouncementRequest;
use Modules\Communication\Repositories\Contracts\AnnouncementRepositoryInterface;

class AnnouncementController extends Controller
{
    protected AnnouncementRepositoryInterface $announcementRepository;

    public function __construct(AnnouncementRepositoryInterface $announcementRepository)
    {
        $this->announcementRepository = $announcementRepository;
    }

    public function index(): View
    {
        return view('communication::announcement.index');
    }

    public function store(AnnouncementRequest $request): JsonResponse
    {
        $result = $this->announcementRepository->store($request);
        return response()->json($result, $result['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $result = $this->announcementRepository->list($request);
        return response()->json($result, $result['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $result = $this->announcementRepository->delete($request);
        return response()->json($result, $result['code']);
    }
}
