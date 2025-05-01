<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Js;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\CarInfo\Models\PricingType;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\InsuranceBenefit;

class InsuranceController extends Controller
{
    public function index(): View
    {
        $priceTypes = PricingType::where('type', 2)->get();
        return view('generalsetting::rental_settings.insurance_list', compact('priceTypes'));
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? '';
        $authUser = current_user();

        $validator = Validator::make($request->all(), [
            'insurance_name' => [
                'required',
                'max:255',
                Rule::unique('insurances')->ignore($id)->whereNull('deleted_at'),
            ],
            'price_type_id' => ['required'],
            'price' => ['required'],
            'benefit.*' => ['required'],
        ], [
            'insurance_name.required' =>  __('admin.general_settings.insurance_name_required'),
            'insurance_name.unique' =>  __('admin.general_settings.insurance_name_exist'),
            'price.required' =>  __('admin.general_settings.price_required'),
            'benefit.*.required' => __('admin.general_settings.benefit_required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ?  __('admin.general_settings.insurance_create_success') :  __('admin.general_settings.insurance_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'insurance_name' => $request->insurance_name,
                'price_type_id' => $request->price_type_id,
                'price' => $request->price,
            ];

            // Set language_id conditionally
            $data['language_id'] = empty($id)
                ? ($authUser->language_id ?? 1)
                : ($request->language_id ?? 1);

            $benefits = $request->benefit ?? [];

            if (empty($id)) {
                $insurance = Insurance::create($data);

                foreach ($benefits as $benefit) {
                    if (!empty($benefit)) {
                        InsuranceBenefit::create([
                            'insurance_id' => $insurance->id,
                            'benefit' => $benefit
                        ]);
                    }
                }
            } else {
                $data['status'] = $request->status ?? 1;
                Insurance::where('id', $id)->update($data);

                // Handle benefit updates
                InsuranceBenefit::where('insurance_id', $id)
                    ->whereNotIn('id', array_keys($benefits))
                    ->delete();

                foreach ($benefits as $key => $benefit) {
                    if ($key === 'new' && is_array($benefit)) {
                        foreach ($benefit as $newBenefit) {
                            if (!empty($newBenefit)) {
                                InsuranceBenefit::create([
                                    'insurance_id' => $id,
                                    'benefit' => $newBenefit
                                ]);
                            }
                        }
                    } else {
                        InsuranceBenefit::where('id', $key)->update([
                            'benefit' => $benefit
                        ]);
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $successMsg
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $errorMsg,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function list(Request $request): JsonResponse
    {
        try {
            $authId = current_user();
            $language_id = $authId->language_id ?? null;
            $query = Insurance::where("language_id", $language_id)->with('insuranceBenefits')->withCount('insuranceBenefits');

            if (!empty($request->search)) {
                $search = $request->search;
                $query->where('insurance_name', 'like', "%{$search}%");
                $query->orWhere('price', 'like', "%{$search}%");
            }

            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'insurance_name';
            $orderDir = $request->order[0]['dir'] ?? 'desc';

            $query->orderBy($columnName, $orderDir);

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;

            $filtertotalRecords = $query->count();
            $totalRecords = Insurance::count();
            $data = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filtertotalRecords,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' =>  __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = Insurance::with('insuranceBenefits')->find($id);

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

            Insurance::where('id', $id)->delete();
            InsuranceBenefit::where('insurance_id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.general_settings.insurance_delete_success'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error'),
            ], 500);
        }
    }

    public function getVehicleInsurances(Request $request):JsonResponse
    {
        try {
            $vehicleId = $request->vehicle_ids;

            $data = Insurance::with(['insuranceBenefits:id,insurance_id,benefit'])->select(
                'insurances.id',
                'insurances.insurance_name',
                'vehicle_insurances.value as insurance_type',
                'vehicle_insurances.price'
            )
                ->withCount('insuranceBenefits')
                ->join('vehicle_insurances', 'vehicle_insurances.insurances_id', '=', 'insurances.id')
                ->whereIn('vehicle_insurances.vehicle_id', $vehicleId)
                ->get()->map(function ($item) {
                    $item->price = number_format($item->price, 0);
                    $item->insurance_type = strtolower($item->insurance_type);
                    return $item;
                });

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => _('admin.common.default_retrieve_error'),
            ], 500);
        }
    }
}
