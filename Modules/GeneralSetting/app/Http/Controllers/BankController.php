<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\GeneralSetting\Models\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BankController extends Controller
{
    public function index(Request $request): View
    {
        return view('generalsetting::bank.index');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? '';

        $validator = Validator::make($request->all(), [
            'bank_name' => ['required'],
            'account_number' => ['required', 'digits_between:8,20'],
            'holder_name' => ['required', 'min:3'],
            'branch' => ['required'],
            'ifsc' => ['required', 'min:5'],
        ], [
            'bank_name.required' => "Bank name is required.",
            'account_number.required' => "Account number is required.",
            'account_number.digits_between' => "Account number must be between 8 and 20 digits.",
            'holder_name.required' => "Account holder name is required.",
            'holder_name.min' => "Account holder name must be at least 3 characters.",
            'branch.required' => "Branch is required.",
            'ifsc.required' => "IFSC is required.",
            'ifsc.min' => "IFSC must be at least 5 characters.",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = __('admin.general_settings.bank_added_successfully');
        $errorMsg = __('admin.general_settings.retrive_error');

        try {
            $default = $request->has('default') && $request->default === "on" ? 1 : 0;

            $data = [
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder_name' => $request->holder_name,
                'branch' => $request->branch,
                'ifsc' => $request->ifsc,
                'default' => $default,
            ];

            if (empty($id)) {
                Bank::create($data);
            } else {
                Bank::where('id', $id)->update($data);
                $successMsg = __('admin.general_settings.bank_update_success');
                $errorMsg = __('admin.general_settings.retrive_error');
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
        $search = $request->input('search');

        try {
            $query = Bank::orderBy('id', $orderBy);
            if (!empty($search)) {
                $query->where('bank_name', 'LIKE', "%{$search}%");
            }

            $data = $query->get();

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.bank_retrive_success'),
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $bank = Bank::find($id);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $bank
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            Bank::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.general_settings.bank_deleted_successfully')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.general_settings.retrive_error')
            ], 500);
        }
    }
}
