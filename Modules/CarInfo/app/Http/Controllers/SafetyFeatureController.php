<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\CarInfo\Models\SafetyFeature;

class SafetyFeatureController extends Controller
{
    public function index(): View
    {
        return view('carinfo::safety_feature.index');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? '';
        $authUser = current_user(); // Ensure this returns the logged-in user
        $languageId = $authUser->language_id ?? 1;
    
        $data = [
            'feature' => $request->feature,
            'language_id' => $languageId
        ];
    
        $validator = Validator::make($request->all(), [
            'feature' => [
                'required',
                'max:100',
                'min:3',
                Rule::unique('safety_features')->ignore($id)->whereNull('deleted_at')
            ],
        ], [
            'feature.required' => __('admin.rentals.feature_required'),
            'feature.max' => __('admin.rentals.feature_maxlength'),
            'feature.min' => __('admin.rentals.feature_minlength'),
            'feature.unique' => __('admin.rentals.feature_unique'),
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }
    
        $successMsg = empty($id) ? __('admin.rentals.safety_feature_create_success') : __('admin.rentals.safety_feature_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');
    
        try {
            if (empty($id)) {
                SafetyFeature::create($data);
            } else {
                $data['status'] = $request->status ?? 1;
                SafetyFeature::where('id', $id)->update($data);
            }
    
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMsg
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMsg,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function list(Request $request): JsonResponse
    {
        try {
            $authUser = current_user(); // Make sure this helper returns the logged-in user
            $languageId = $authUser->language_id ?? 1;
            $query = SafetyFeature::query()->where("language_id", $languageId);
    
            // Search
            if (!empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('feature', 'like', "%{$search}%");
                });
            }
    
            // Status Filter
            if ($request->has('sort_by_status') && !empty($request->sort_by_status) || $request->sort_by_status == '0') {
                $status = $request->sort_by_status;
                $query->where('safety_features.status', $status);
            }
    
            // Ordering
            $columnIndex = $request->order[0]['column'] ?? 1;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'feature';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            if (in_array($columnName, ['feature', 'status'])) {
                $query->orderBy($columnName, $orderDir);
            } else {
                $query->orderBy('feature', 'asc');
            }
    
            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
    
            // Total records count
            $filterTotalRecords = $query->count();
            $totalRecords = SafetyFeature::where("language_id", $languageId)->count();
    
            // Get data
            $data = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filterTotalRecords,
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
        $doorType = SafetyFeature::find($id);
        
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $doorType
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            SafetyFeature::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.safety_feature_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ],500);
        }
    }
}
