<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use Dompdf\Css\Content\Counter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StateController extends Controller
{
    public function index(): View
    {
        $country_ids = Country::select("id", "name")->get();

        return view('admin.state.index', compact("country_ids"));
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? null;

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:255',
                Rule::unique('states')->where(function ($query) use ($request, $id) {
                    return $query->where('country_id', $request->country_id)->when($id, function ($q) use ($id) {
                        return $q->where('id', '!=', $id);
                    });
                })
            ],
            'country_id' => [
                'required',
                'exists:countries,id'
            ],
        ], [
            'name.required' =>  __('admin.cms.state_required'),
            'name.unique' =>  __('admin.cms.state_exists'),
            'country_id.required' => __('admin.cms.country_required'),
            'country_id.exists' => __('admin.cms.country_exists'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.cms.state_create_success') : __('admin.cms.state_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'name' => $request->name,
                'country_id' => $request->country_id,
                'status' => $request->status ?? 1
            ];

            if (empty($id)) {
                State::create($data);
            } else {
                State::where('id', $id)->update($data);
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMsg
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMsg
            ], 500);
        }
    }


    public function list(Request $request): JsonResponse
    {
        $orderBy = $request->order_by ?? 'desc';

        try {
            $data = State::with('country')
                ->when($request->search, function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->search}%");
                })
                ->orderBy('id', $orderBy)
                ->get();

            return response()->json([
                'code' => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $data,
            ], 200);
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
        $state = State::find($id);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $state
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;

            State::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.cms.state_delete_success'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error'),
            ], 500);
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $ids = $request->ids;

        if (!$ids || count($ids) == 0) {
            return response()->json(['success' => false, 'message' => 'No items selected.']);
        }

        State::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => __('admin.cms.state_delete_success')]);
    }
}
