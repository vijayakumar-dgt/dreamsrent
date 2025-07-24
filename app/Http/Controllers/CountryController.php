<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCountryRequest;
use App\Http\Requests\EditCountryRequest;
use App\Repositories\Contracts\CountryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CountryController extends Controller
{
    protected $countryRepository;

    public function __construct(CountryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function index(): View
    {
        return view('admin.country.index');
    }

    public function store(AddCountryRequest $request): JsonResponse
    {
        try {
            $data = [
                'name'   => $request->name,
                'code'   => $request->code,
                'status' => (int) ($request->status ?? 1),
            ];
            // dd($data);
            if ($request->filled('id')) {
                $this->countryRepository->update($request->id, $data);
                $message = __('admin.cms.country_update_success');
            } else {
                $this->countryRepository->create($data);
                $message = __('admin.cms.country_create_success');
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => $request->filled('id')
                    ? __('admin.common.default_update_error')
                    : __('admin.common.default_create_error'),
            ], 500);
        }
    }

    public function update(EditCountryRequest $request): JsonResponse
    {
        try {
            $data = [
                'name'   => $request->name,
                'code'   => $request->code,
                'status' => $request->status ?? 1
            ];

            $this->countryRepository->update($request->id, $data);

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.cms.country_update_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_update_error')
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $data = $this->countryRepository->search(
                $request->search,
                $request->status,
                $request->order_by ?? 'desc'
            );

            return response()->json([
                'code'    => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request): JsonResponse
    {
        $country = $this->countryRepository->find($request->id);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $country
        ]);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $this->countryRepository->delete($request->id);

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.common.default_delete_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
            ], 500);
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $ids = $request->ids;

        if (!$ids || count($ids) == 0) {
            return response()->json([
                'success' => false,
                'message' => __('admin.common.no_data_found')
            ]);
        }

        $this->countryRepository->bulkDelete($ids);

        return response()->json([
            'success' => true,
            'message' => __('admin.common.default_delete_success')
        ]);
    }
}
