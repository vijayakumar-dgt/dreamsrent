<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Js;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\GeneralSetting\Models\SubTax;
use Modules\GeneralSetting\Models\TaxGroup;
use Modules\GeneralSetting\Models\TaxRate;

class TaxRateController extends Controller
{
    public function index(): View
    {
        return view('generalsetting::finance_settings.tax_rates');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? '';

        $validator = Validator::make($request->all(), [
            'tax_name' => [
                'required',
                'max:30',
                'min:3',
                Rule::unique('tax_rates')->ignore($id)->whereNull('deleted_at')
            ],
            "tax_rate" => "required",
        ], [
            'tax_name.required' => __('admin.general_settings.tax_name_required'),
            'tax_name.min' => __('admin.general_settings.tax_name_minlength'),
            'tax_name.max' => __('admin.general_settings.tax_name_maxlength'),
            'tax_name.unique' => __('admin.general_settings.tax_name_unique'),
            'tax_rate.required' => __('admin.general_settings.tax_rate_required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.general_settings.tax_rate_create_success') : __('admin.general_settings.tax_rate_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'tax_name' => $request->tax_name,
                'tax_rate' => $request->tax_rate,
            ];

            if (empty($id)) {
                TaxRate::create($data);
            } else {
                $data['status'] = $request->status ?? 1;
                TaxRate::where('id', $id)->update($data);
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
            $orderBy = $request->order_by ?? 'asc';

            $data = TaxRate::orderBy('id', $orderBy)->get()->map(function ($taxRate) {
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
        $data = TaxRate::find($id);

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
            TaxRate::where('id', $id)->delete();

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

    public function taxGroupStore(Request $request): JsonResponse
    {
        $id = $request->id ?? '';

        $validator = Validator::make($request->all(), [
            'tax_group_name' => [
                'required',
                'max:30',
                'min:3',
                Rule::unique('tax_groups', 'tax_name')->ignore($id)->whereNull('deleted_at')
            ],
            "sub_tax" => "required",
        ], [
            'tax_group_name.required' => __('admin.general_settings.tax_group_name_required'),
            'tax_group_name.min' => __('admin.general_settings.tax_group_name_minlength'),
            'tax_group_name.max' => __('admin.general_settings.tax_group_name_maxlength'),
            'tax_group_name.unique' => __('admin.general_settings.tax_group_name_unique'),
            'sub_tax.required' => __('admin.general_settings.sub_taxes_required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.general_settings.tax_group_create_success') : __('admin.general_settings.tax_group_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'tax_name' => $request->tax_group_name,
            ];

            if (empty($id)) {
                $taxGroup = TaxGroup::create($data);
                if ($taxGroup && !empty($request->sub_tax)) {
                    foreach ($request->sub_tax as $taxRateId) {
                        SubTax::updateOrCreate(
                            ['tax_group_id' => $taxGroup->id, 'tax_rate_id' => $taxRateId],
                            ['tax_group_id' => $taxGroup->id, 'tax_rate_id' => $taxRateId]
                        );
                    }
                }
            } else {
                $data['status'] = $request->status ?? 1;
                $taxGroup = TaxGroup::where('id', $id)->update($data);

                if ($taxGroup && !empty($request->sub_tax)) {
                    SubTax::where('tax_group_id', $id)->whereNotIn('tax_rate_id', $request->sub_tax)->delete();
                    foreach ($request->sub_tax as $taxRateId) {
                        SubTax::updateOrCreate(
                            ['tax_group_id' => $id, 'tax_rate_id' => $taxRateId],
                            ['tax_group_id' => $id, 'tax_rate_id' => $taxRateId]
                        );
                    }
                }
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

    public function taxGroupList(Request $request): JsonResponse
    {
        try {
            $orderBy = $request->order_by ?? 'asc';

            $data = TaxGroup::with(['taxRates:id,tax_name,tax_rate'])->orderBy('id', $orderBy)->get()->map(function ($tax) {
                $tax->created_on = formatDateTime($tax->created_at, false);
                $tax->total_tax_rate = $tax->taxRates ? number_format($tax->taxRates->sum('tax_rate'), 2) : 0;
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
        $data = TaxGroup::with('taxRates')->find($id);

        if ($data->taxRates) {
            $data['total_tax_rate'] = $data->taxRates->sum('tax_rate');
        }

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
            TaxGroup::where('id', $id)->delete();

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
            $data = TaxRate::where('status', 1)->get(['id', 'tax_name', 'tax_rate']);

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
