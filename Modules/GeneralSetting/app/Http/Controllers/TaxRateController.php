<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\GeneralSetting\Http\Requests\StoreTaxRateRequest;
use Modules\GeneralSetting\Http\Requests\StoreTaxGroupRequest;
use Modules\GeneralSetting\Repositories\Contracts\TaxRateSettingInterface;
use Illuminate\View\View;

class TaxRateController extends Controller
{
    protected $taxRateSettingRepository;

    public function __construct(TaxRateSettingInterface $taxRateSettingRepository)
    {
        $this->taxRateSettingRepository = $taxRateSettingRepository;
    }

    public function index(): View
    {
        return view('generalsetting::finance_settings.tax_rates');
    }

    public function store(StoreTaxRateRequest $request): JsonResponse
    {
        try {
            $data = [
                'tax_name' => $request->tax_name,
                'tax_rate' => $request->tax_rate,
                'status' => $request->status ?? 1,
            ];

            if (isset($request->id)) {
                $data['id'] = $request->id;
            }

            $success = $this->taxRateSettingRepository->createOrUpdateTaxRate($data);

            if ($success) {
                $message = isset($request->id) 
                    ? __('admin.general_settings.tax_rate_update_success') 
                    : __('admin.general_settings.tax_rate_create_success');
                
                return response()->json([
                    'status' => 'success',
                    'code'   => 200,
                    'message' => $message
                ]);
            }

            throw new \Exception('Failed to save tax rate');

        } catch (\Exception $e) {
            $message = isset($request->id) 
                ? __('admin.common.default_update_error') 
                : __('admin.common.default_create_error');
            
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $message,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $orderBy = $request->order_by ?? 'asc';
            $data = $this->taxRateSettingRepository->getTaxRateList($orderBy)
                ->map(function ($taxRate) {
                    $taxRate->created_on = formatDateTime($taxRate->created_at, false);
                    return $taxRate;
                });

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
        $data = $this->taxRateSettingRepository->findTaxRate($id);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $data
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            $this->taxRateSettingRepository->deleteTaxRate($id);

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.general_settings.tax_rate_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }

    public function taxGroupStore(StoreTaxGroupRequest $request): JsonResponse
    {
        try {
            $data = [
                'tax_group_name' => $request->tax_group_name,
                'sub_tax' => $request->sub_tax,
                'status' => $request->status ?? 1,
            ];

            if (isset($request->id)) {
                $data['id'] = $request->id;
            }

            $success = $this->taxRateSettingRepository->createOrUpdateTaxGroup($data);

            if ($success) {
                $message = isset($request->id)
                    ? __('admin.general_settings.tax_group_update_success')
                    : __('admin.general_settings.tax_group_create_success');
                
                return response()->json([
                    'status' => 'success',
                    'code'   => 200,
                    'message' => $message
                ]);
            }

            throw new \Exception('Failed to save tax group');

        } catch (\Exception $e) {
            $message = isset($request->id)
                ? __('admin.common.default_update_error')
                : __('admin.common.default_create_error');
            
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $message,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function taxGroupList(Request $request): JsonResponse
    {
        try {
            $orderBy = $request->order_by ?? 'asc';
            $data = $this->taxRateSettingRepository->getTaxGroupList($orderBy)
                ->map(function ($tax) {
                    $tax->created_on = formatDateTime($tax->created_at, false);
                    $tax->total_tax_rate = number_format($tax->taxRates->sum('tax_rate'), 2);
                    return $tax;
                });

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

    public function taxGroupEdit(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = $this->taxRateSettingRepository->findTaxGroup($id);

        if (!$data) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' => 'Tax group not found.',
                'data'    => null,
            ], 404);
        }

        $data['total_tax_rate'] = $data->taxRates->sum('tax_rate');

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $data
        ], 200);
    }

    public function taxGroupDelete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            $this->taxRateSettingRepository->deleteTaxGroup($id);

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.general_settings.tax_group_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }

    public function getTaxRates(Request $request): JsonResponse
    {
        try {
            $data = $this->taxRateSettingRepository->getActiveTaxRates();

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
}