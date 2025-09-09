<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\SeasonRequest;
use Modules\CarInfo\Repositories\Contracts\SeasonRepositoryInterface;

class SeasonController extends Controller
{
    protected SeasonRepositoryInterface $seasonRepository;

    public function __construct(SeasonRepositoryInterface $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function index(): View
    {
        return view('carinfo::season.index');
    }

    public function save(SeasonRequest $request): JsonResponse
    {
        $result = $this->seasonRepository->save($request);
        return response()->json($result, $result['code']);
    }

    public function getSeasons(Request $request): JsonResponse
    {
        $result = $this->seasonRepository->getSeasons($request);
        return response()->json($result, $result['code']);
    }

    public function getSeason($id): JsonResponse
    {
        $response = $this->seasonRepository->getSeason($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $response = $this->seasonRepository->delete($request);
        return response()->json($response, $response['code']);
    }
}
