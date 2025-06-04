<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCityRequest;
use App\Http\Requests\EditCityRequest;
use App\Repositories\Contracts\CityInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class CityController extends Controller
{
    protected $cityRepository;

    public function __construct(CityInterface $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function index(): View
    {
        $state_ids = $this->cityRepository->getStates();
        return view('admin.city.index', compact("state_ids"));
    }

    public function store(AddCityRequest $request): JsonResponse
    {
        try {
            $data = [
                'name' => $request->name,
                'state_id' => $request->state_id,
                'status' => (int) ($request->status ?? 1),
            ];

            if ($request->filled('id')) {
                $this->cityRepository->update($request->id, $data);
                $message = __('admin.cms.city_update_success');
            } else {
                $this->cityRepository->create($data);
                $message = __('admin.cms.city_create_success');
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $request->filled('id')
                    ? __('admin.common.default_update_error')
                    : __('admin.common.default_create_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $params = [
                'start' => $request->input('start'),
                'length' => $request->input('length'),
                'search' => $request->input('search.value'),
                'order_column' => $request->input("columns.{$request->input('order.0.column')}.data") ?? 'name',
                'order_dir' => $request->input('order.0.dir') ?? 'asc',
                'status' => $request->input('status'),
            ];

            $result = $this->cityRepository->datatable($params);

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $result['total'],
                'recordsFiltered' => $result['filtered'],
                'data' => $result['data'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request): JsonResponse
    {
        $city = $this->cityRepository->find($request->id);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $city
        ]);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $this->cityRepository->delete($request->id);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.cms.city_delete_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.common.default_delete_error')
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

        $this->cityRepository->bulkDelete($ids);

        return response()->json([
            'success' => true,
            'message' => __('admin.cms.city_delete_success')
        ]);
    }
}
