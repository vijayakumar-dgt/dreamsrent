<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\InsuranceRequest;
use Modules\GeneralSetting\Repositories\Contracts\InsuranceSettingInterface;

class InsuranceController extends Controller
{
    protected InsuranceSettingInterface $repository;

    public function __construct(InsuranceSettingInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(): View
    {
        $priceTypes = $this->repository->getPricingTypes();
        return view('generalsetting::rental_settings.insurance_list', compact('priceTypes'));
    }

    public function store(InsuranceRequest $request): JsonResponse
    {
        try {
            $id = $request->id ?? null;
            $data = $request->only([
                'insurance_name',
                'price_type_id',
                'price',
                'status',
                'language_id'
            ]);
            $data['status'] = ($request->has('status') && $request->status == 1 || empty($id)) ? 1 : 0;
            $data['language_id'] = $data['language_id'] ?? 1;

            $insurance = $this->repository->saveInsurance(
                $data,
                $request->benefit ?? [],
                $request->id ?? null
            );

            $successMsg = empty($id)
                ? __('admin.general_settings.insurance_create_success')
                : __('admin.general_settings.insurance_update_success');

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => $successMsg
            ]);
        } catch (\Exception $e) {
            $errorMsg = empty($id)
                ? __('admin.common.default_create_error')
                : __('admin.common.default_update_error');

            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => $errorMsg,
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $result = $this->repository->getInsuranceList($request->all());
            return response()->json($result, 200);
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
        try {
            $data = $this->repository->getInsuranceWithBenefits($request->id);
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $this->repository->deleteInsurance($request->id);
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.insurance_delete_success'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
            ], 500);
        }
    }

    public function getVehicleInsurances(Request $request): JsonResponse
    {
        try {
            $data = $this->repository->getVehicleInsurances($request->vehicle_ids);
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ], 500);
        }
    }
}
