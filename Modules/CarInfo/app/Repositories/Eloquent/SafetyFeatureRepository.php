<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Http\Requests\SafetyFeatureRequest;
use Modules\CarInfo\Models\CarFuel;
use Modules\CarInfo\Models\Cylinder;
use Modules\CarInfo\Models\SafetyFeature;
use Modules\CarInfo\Repositories\Contracts\SafetyFeatureRepositoryInterface;


class SafetyFeatureRepository implements SafetyFeatureRepositoryInterface
{
   public function store(SafetyFeatureRequest $request): array
    {
        $id = $request->id ?? '';
        $authUser = current_user();
        $languageId = $authUser->language_id ?? 1;

        $data = [
            'feature' => $request->feature,
            'language_id' => $languageId
        ];

        $successMsg = empty($id)
            ? __('admin.rentals.safety_feature_create_success')
            : __('admin.rentals.safety_feature_update_success');

        $errorMsg = empty($id)
            ? __('admin.common.default_create_error')
            : __('admin.common.default_update_error');

        try {
            if (empty($id)) {
                SafetyFeature::create($data);
            } else {
                $data['status'] = $request->status ?? 1;
                SafetyFeature::where('id', $id)->update($data);
            }

            return [
                'status' => 'success',
                'code' => 200,
                'message' => $successMsg
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $errorMsg,
                'error' => $e->getMessage()
            ];
        }
    }


    public function list(Request $request): array
    {
        try {
            $authUser = current_user();
            $languageId = $authUser->language_id ?? 1;
            $query = SafetyFeature::query()->where("language_id", $languageId);

            if (!empty($request->search)) {
                $query->where(function ($q) use ($request) {
                    $q->where('feature', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->has('sort_by_status') && ($request->sort_by_status !== null || $request->sort_by_status == '0')) {
                $query->where('safety_features.status', $request->sort_by_status);
            }

            $columnIndex = $request->order[0]['column'] ?? 1;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'feature';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            if (in_array($columnName, ['feature', 'status'])) {
                $query->orderBy($columnName, $orderDir);
            } else {
                $query->orderBy('feature', 'asc');
            }

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;

            $filterTotalRecords = $query->count();
            $totalRecords = SafetyFeature::where("language_id", $languageId)->count();
            $data = $query->skip($start)->take($length)->get();

            return [
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filterTotalRecords,
                'data' => $data,
                'code' => 200,
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function edit(int $id): array
    {
        $feature = SafetyFeature::find($id);

        return [
            'status' => 'success',
            'code' => 200,
            'data' => $feature
        ];
    }

    public function delete(int $id): array
    {
        try {
            SafetyFeature::where('id', $id)->delete();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.rentals.safety_feature_delete_success')
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.common.default_delete_error')
            ];
        }
    }

 


}