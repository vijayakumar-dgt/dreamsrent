<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\StoreCurrencyRequest;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Repositories\Contracts\CurrencySettingInterface;

class CurrencyController extends Controller
{
    protected $currencySettingRepository;

    public function __construct(CurrencySettingInterface $currencySettingRepository)
    {
        $this->currencySettingRepository = $currencySettingRepository;
    }

    public function index(): View
    {
        return view('generalsetting::finance_settings.currencies', [
            'page_title' => 'Currencies'
        ]);
    }

    public function save_currency(StoreCurrencyRequest $request): JsonResponse
    {
        try {
            $data = [
                'currency_name' => $request->currency_name,
                'code'          => $request->code,
                'symbol'        => $request->symbol,
                'exchange_rate' => $request->exchange_rate ?? 0,
                'status'        => $request->status === 'on' ? 1 : 0,
            ];

            if ($request->has('id')) {
                $data['id'] = $request->id;
            }

            $success = $this->currencySettingRepository->createOrUpdateCurrency($data);

            if ($success) {
                $message = $request->has('id')
                    ? __('admin.general_settings.currency_updated_successfully')
                    : __('admin.general_settings.currency_created_successfully');

                return response()->json([
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => $message
                ]);
            }

            throw new \Exception('Failed to save currency');
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getCurrencies(Request $request): JsonResponse
    {
        try {
            $filters = [
                'keyword'  => $request->keyword ?? '',
                'order_by' => 'asc',
                'paginate' => false
            ];

            // Get all filtered currencies without pagination
            $currencies = $this->currencySettingRepository->getCurrencyList($filters);

            if ($request->has('draw')) {
                $recordsFiltered = $currencies->count();
                $recordsTotal = Currency::count();

                return response()->json([
                    'draw'            => intval($request->draw),
                    'recordsTotal'    => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data'            => $currencies
                ]);
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'data'   => $currencies
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function editCurrency(?int $id): JsonResponse
    {
        try {
            $currency = $this->currencySettingRepository->findCurrency($id);

            if (!$currency) {
                throw new \Exception(__('admin.general_settings.currency_not_found'));
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'data'    => $currency,
                'message' => __('admin.general_settings.currency_fetched_successfully')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' => $th->getMessage()
            ], 404);
        }
    }

    public function deleteCurrency(Request $request): JsonResponse
    {
        try {
            $deleted = $this->currencySettingRepository->deleteCurrency($request->id);

            if (!$deleted) {
                throw new \Exception(__('admin.general_settings.currency_not_found'));
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.currency_deleted_successfully')
            ]);
        } catch (\Throwable $th) {
            $code = $th->getMessage() === __('admin.general_settings.currency_not_found') ? 404 : 500;

            return response()->json([
                'status'  => 'error',
                'code'    => $code,
                'message' => $th->getMessage()
            ], $code);
        }
    }
}
