<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CityController extends Controller
{
    public function index(): View
    {
        $state_ids = State::select("id", "name")->get();

        return view('admin.city.index', compact("state_ids"));
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? null;

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:255',
                Rule::unique('cities')->where(function ($query) use ($request, $id) {
                    return $query->where('state_id', $request->state_id)->when($id, function ($q) use ($id) {
                        return $q->where('id', '!=', $id);
                    });
                })
            ],
            'state_id' => [
                'required',
                'exists:states,id'
            ],
        ], [
            'name.required' => __('admin.cms.city_required'),
            'name.unique' => __('admin.cms.city_exists'),
            'state_id.required' => __('admin.cms.state_required'),
            'state_id.exists' => __('admin.cms.state_exists'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.cms.city_create_success') : __('admin.cms.city_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'name' => $request->name,
                'state_id' => $request->state_id,
                'status' => $request->status ?? 1
            ];

            if (empty($id)) {
                City::create($data);
            } else {
                City::where('id', $id)->update($data);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $successMsg
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $errorMsg,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        $orderBy = $request->order_by ?? 'asc';

        try {
            $length = $request->input('length');
            $start = $request->input('start');
            $search = $request->input('search');
            $orderByColumnIndex = $request->input('order.0.column');
            $orderByColumn = $request->input("columns.$orderByColumnIndex.data") ?? 'name';
            $orderDirection = $request->input('order.0.dir') ?? 'asc';
            $query = City::with(['state.country']);

            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('state', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhereHas('country', function ($qc) use ($search) {
                                $qc->where('name', 'like', "%{$search}%");
                            });
                    });
            }

            if ($request->has('status') && !empty($request->status) || $request->status == '0') {
                $status = $request->status;
                $query->where('status', $status);
            }

            $total = $query->count();
            $cities = $query->orderBy($orderByColumn, $orderDirection)
                ->skip($start)
                ->take($length)
                ->get();

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $cities,
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
        $id = $request->id;
        $city = City::find($id);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $city
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            City::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.cms.city_delete_success')
            ], 200);
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
            return response()->json(['success' => false, 'message' => 'No items selected.']);
        }

        City::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => __('admin.cms.city_delete_success')]);
    }
}
