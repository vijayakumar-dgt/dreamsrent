<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\GeneralSetting\Models\Currency;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():View
    {
        $data = [
            'page_title' => 'Currencies'
        ];
        return view('generalsetting::finance_settings.currencies', $data);
    }

    public function save_currency(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'currency_name' => 'required|unique:currencies,currency_name,' . $request->id . ',id,deleted_at,NULL',
            'exchange_rate' => 'required|numeric|min:0',
            'code'          => 'required',
            'symbol'        => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                 'status' => 'error',
                 'code'   => 422,
                 'message' => __('admin.general_settings.validation_error'),
                 'errors' => $validator->errors()->toArray()
            ], 422);
        }
        try {
            $successMessage = "";
            if ($request->has('id') && $request->id != "") {
                $currency = Currency::find($request->id);
                $currency->status         = $request->status == 'on' ? 1 : 0;
                $successMessage = __('admin.general_settings.currency_created_successfully');
            } else {
                $currency = new Currency();
                $successMessage = __('admin.general_settings.currency_updated_successfully');
            }
            $currency->currency_name  = $request->currency_name;
            $currency->code           = $request->code;
            $currency->symbol         = $request->symbol;
            $currency->exchange_rate  = $request->exchange_rate;
            $currency->save();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMessage
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function getCurrencies(Request $request):JsonResponse
    {
        $pageLength = $request->length;
        $offset     = $request->start;
        $currencies = Currency::query();
        if ($request->has('keyword') && $request->keyword != "") {
            $currencies = $currencies->where(function ($query) use ($request) {
                          $query->where('currency_name', 'like', '%' . $request->keyword . '%')
                                ->orWhere('code', 'like', '%' . $request->keyword . '%');
            });
        }
        $currencies = $currencies->orderBy('currency_name', 'asc');
        $currencies = $currencies->skip($offset)->take($pageLength)->get();
        $totalRecords = $filteredRecords = Currency::count();
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $currencies
        ]);
    }

    public function editCurrency($id):JsonResponse
    {
        $currency = Currency::find($id);
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $currency,
            'message' => __('admin.general_settings.currency_fetched_successfully')
        ]);
    }

    public function deleteCurrency(Request $request):JsonResponse
    {
        try {
            $currency = Currency::findOrFail($request->id);
            $currency->delete();
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' =>  __('admin.general_settings.currency_deleted_successfully')
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' =>  __('admin.general_settings.currency_not_found')
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage()
            ], 422);
        }
    }
}
