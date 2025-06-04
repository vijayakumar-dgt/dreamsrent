<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\CarInfo\Models\Season;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Modules\CarInfo\Http\Requests\SeasonRequest;
use Modules\CarInfo\Repositories\Contracts\SeasonRepositoryInterface;

class SeasonController extends Controller
{
    protected SeasonRepositoryInterface $SeasonRepository;

    public function __construct(SeasonRepositoryInterface $SeasonRepository)
    {
        $this->SeasonRepository = $SeasonRepository;
    }

    public function index(): View
    {
        return view('carinfo::season.index');
    }

    public function save(SeasonRequest $request): JsonResponse
    {
        $result = $this->SeasonRepository->save($request);
        return response()->json($result, $result['code']);
    }

    public function getSeasons(Request $request): JsonResponse
    {
        $result = $this->SeasonRepository->getSeasons($request);
        return response()->json($result, $result['code']);
    }

    public function getSeason($id): JsonResponse
    {
        $response = $this->SeasonRepository->getSeason($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $response = $this->SeasonRepository->delete($request);
        return response()->json($response, $response['code']);
    }
}
