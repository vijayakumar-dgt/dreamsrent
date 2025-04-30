<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\CarInfo\Models\PricingType;

class PricingTypeController extends Controller
{
    public function index()
    {
        return view('carinfo::pricing_type.index');
    }

    public function store(Request $request)
    {
        $id = $request->id ?? '';
        $data = [
            'pricing_type' => strtolower($request->pricing_type),
        ];

        $validator = Validator::make($request->all(), [
            'pricing_type' => [
                'required',
                'max:255',
                Rule::unique('pricing_types')->ignore($id)->whereNull('deleted_at')
            ],
        ], [
            'pricing_type.required' => "Pricing type is required.",
            'pricing_type.unique' => "Pricing type already exists.",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = "Pricing type added successfully.";
        $errorMsg = "An error occured while adding!";

        try {
            if (empty($id)) {
                PricingType::create($data);
            } else {
                $successMsg = "Pricing type updated successfully.";
                $errorMsg = "An error occured while updating!";
                $data['status'] = $request->status ?? 1;

                PricingType::where('id', $id)->update($data);
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

    public function list(Request $request)
    {
        $orderBy = $request->order_by ?? 'desc';

        try {
            $data = PricingType::orderBy('id', $orderBy)->get();

            return response()->json([
                'code' => 200,
                'message' => __('Pricing types retrieved successfully.'),
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('An error occurred while retrieving!'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $doorType = PricingType::find($id);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $doorType
        ], 200);
    }

    public function delete(Request $request)
    {
        try {
            $id = $request->id;

            PricingType::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => 'Pricing tyoe deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => 'An error occured while deleting!'
            ], 500);
        }
    }
}
