<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\Enquiry;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Modules\CarInfo\Http\Requests\EnquiryRequest;
use Modules\CarInfo\Http\Requests\EnquiryUpdateRequest;
use Modules\CarInfo\Repositories\Contracts\EnquiryRepositoryInterface;

class EnquireController extends Controller
{
    protected EnquiryRepositoryInterface $enquiryRepository;

    public function __construct(EnquiryRepositoryInterface $enquiryRepository)
    {
        $this->enquiryRepository = $enquiryRepository;
    }

    public function index(): View
    {
        return view('carinfo::car_enquires.index');
    }

    public function store(EnquiryRequest $request): JsonResponse
    {
        $response = $this->enquiryRepository->store($request);
        return response()->json($response, $response['code']);
    }
    public function list(Request $request): JsonResponse
    {
        $response = $this->enquiryRepository->getAll($request);
        return response()->json($response, $response['code']);
    }
    public function update(EnquiryUpdateRequest $request): JsonResponse
    {
        $response = $this->enquiryRepository->update($request);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->enquiryRepository->delete($id);
        return response()->json($response, $response['code']);
    }
}
