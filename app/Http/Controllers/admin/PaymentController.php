<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PaymentInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    protected $paymentRepository;

    public function __construct(PaymentInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function index(): View
    {
        $getPayments = $this->paymentRepository->getDistinctPaymentTypes();
        return view("admin.payment.index", ['GetPayments' => $getPayments]);
    }

    public function paymentList(Request $request): JsonResponse
    {
        try {
            $params = [
                'start'          => $request->input('start', 0),
                'length'         => $request->input('length', 10),
                'search'         => $request->input('search.value'),
                'sort_column'    => $request->input("columns.{$request->input('order.0.column')}.data") ?? 'id',
                'sort_direction' => $request->input('order.0.dir', 'desc'),
                'payment_status' => $request->payment_status ?? [],
                'payment_type'   => $request->payment_type ?? [],
                'sortby'         => $request->sortby,
            ];

            $result = $this->paymentRepository->getPaymentList($params);

            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => $result['total'],
                'recordsFiltered' => $result['filtered'],
                'data'            => $result['data'],
                'currency_symbol' => getDefaultCurrencySymbol(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
