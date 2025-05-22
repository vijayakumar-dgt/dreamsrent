<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CountryController extends Controller
{
    public function index(): View
    {
        return view('admin.country.index');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? null;

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:255',
                Rule::unique('countries')->ignore($id)
            ],
        ], [
            'name.required' => __('admin.cms.country_required'),
            'name.unique' => __('admin.cms.country_exists'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.cms.country_create_success') : __('admin.cms.country_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'name' => $request->name,
                'code' => $request->code,
                'status' => $request->status ?? 1
            ];

            if (empty($id)) {
                Country::create($data);
            } else {
                Country::where('id', $id)->update($data);
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
                'message' => $errorMsg
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        $orderBy = $request->order_by ?? 'desc';

        try {
            $data = Country::when($request->search, function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->search}%");
            })
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);
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
        $country = Country::find($id);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $country
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;

            Country::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.common.default_delete_success'),
            ], 200);
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
            return response()->json(['success' => false, 'message' => __('admin.common.no_data_found'),]);
        }

        Country::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => __('admin.common.default_delete_success')]);
    }
}
