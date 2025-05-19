<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingUserInfo;

class PaymentController extends Controller
{
    public function index(): View
    {
        $GetPayments = Booking::select("payment_type")->distinct()->pluck("payment_type")->toArray();

        return view("admin.payment.index", compact('GetPayments'));
    }

    public function paymentList(Request $request): JsonResponse
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search', null);
        $sortColumnIndex = $request->input('order.0.column', 0);
        $sortDirection = $request->input('order.0.dir', 'desc');
        $columns = ['id', 'name', 'final_price', 'payment_type', 'created_at', 'payment_status'];
        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $paymentStatuses = $request->payment_status ?? [];
        $paymentTypes = $request->payment_type ?? [];

        try {
            $query = Booking::with(['userInfo', 'customerDetail:user_id,profile_image'])
                ->where('booking_by', 'user');

            if (!empty($paymentTypes)) {
                $query->whereIn('payment_type', $paymentTypes);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('userInfo', function ($sub) use ($search) {
                        $sub->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('reservation_id', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('payment_type', 'LIKE', "%{$search}%");
                });
            }

            if (!empty($paymentStatuses)) {
                $query->whereIn('payment_status', $paymentStatuses);
            }

            $totalData = $query->count();

            if ($request->has('sortby') && !empty($request->sortby)) {
                switch (strtolower($request->sortby)) {
                    case 'latest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'asc':
                        $query->orderBy('id', 'asc');
                        break;
                    case 'desc':
                        $query->orderBy('id', 'desc');
                        break;
                    case 'last_month':
                        $startDate = \Carbon\Carbon::now()->subMonth()->startOfMonth();
                        $endDate = \Carbon\Carbon::now()->subMonth()->endOfMonth();
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                        break;
                    case 'last_7_days':
                        $startDate = \Carbon\Carbon::now()->subDays(7)->startOfDay();
                        $endDate = \Carbon\Carbon::now()->endOfDay();
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                        break;
                }
            }

            $query->orderBy($sortColumn === 'name' ? 'id' : $sortColumn, $sortDirection);

            $bookings = $query->skip($start)->take($length)->get();

            $data = $bookings->map(function (Booking $booking): array {
                $userInfo = $booking->userInfo;
                $createdAt = $booking->created_at;
                $paymentType = '';

                if ($booking->payment_type == 'cod') {
                    $paymentType = strtoupper(str_replace('_', ' ', (string)$booking->payment_type));
                } else {
                    $paymentType = ucfirst(str_replace('_', ' ', (string)$booking->payment_type));
                }

                return [
                    'id' => $booking->reservation_id,
                    'name' => $userInfo ? ucfirst($userInfo->first_name) . " " . ucfirst($userInfo->last_name) : "-",
                    'profile_image' => $booking->customerDetail->profile_image ? uploadedAsset($booking->customerDetail->profile_image) : uploadedAsset('', 'profile'),
                    'amount' => $booking->final_price,
                    'payment_type' => $paymentType,
                    'created_at' => formatDateTime($createdAt, false),
                    'payment_status' => $booking->payment_status,
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalData,
                'data' => $data,
                'currency_symbol' => getDefaultCurrencySymbol(),
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
