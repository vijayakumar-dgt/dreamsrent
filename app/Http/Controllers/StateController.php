<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddStateRequest;
use App\Http\Requests\EditStateRequest;
use App\Repositories\Contracts\StateInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class StateController extends Controller
{
    protected $stateRepository;

    public function __construct(StateInterface $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    public function index(): View
    {
        $country_ids = $this->stateRepository->getCountries();
        return view('admin.state.index', compact("country_ids"));
    }
    public function store(AddStateRequest $request): JsonResponse
    {
        try {
            $data = [
                'name' => $request->name,
                'country_id' => $request->country_id,
                'status' => (int) ($request->status ?? 1),
            ];

            if ($request->filled('id')) {
                $this->stateRepository->update($request->id, $data);
                $message = __('admin.cms.state_update_success');
            } else {
                $this->stateRepository->create($data);
                $message = __('admin.cms.state_create_success');
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
            ], 500);
        }
    }


    public function list(Request $request): JsonResponse
    {
        try {
            $data = $this->stateRepository->search(
                $request->search,
                $request->status,
                $request->order_by ?? 'desc'
            );

            return response()->json([
                'code' => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $data,
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
        $state = $this->stateRepository->find($request->id);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $state
        ]);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $this->stateRepository->delete($request->id);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.cms.state_delete_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
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

        $this->stateRepository->bulkDelete($ids);

        return response()->json([
            'success' => true, 
            'message' => __('admin.cms.state_delete_success')
        ]);
    }
}