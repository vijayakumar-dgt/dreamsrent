<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingUserInfo;
use Modules\CarInfo\Models\VehicleInfo;

class PaymentController extends Controller
{
    public function index()
    {
        $GetPayments = Booking::select("payment_type")->distinct()->pluck("payment_type")->toArray();

        return view("admin.payment.index", compact('GetPayments'));
    }


    public function paymentList(Request $request)
    {
        $sortby = $request->sortby ?? 'latest';
        $search = $request->search ?? null;
        $paymentStatuses = $request->payment_status ?? [];
        $paymentTypes = $request->payment_type ?? [];
        $bookingBy = $request->booking_by ?? 'user';

        try {
            $query = Booking::with('userInfo');

            if (!empty($paymentTypes)) {
                $query->whereIn('payment_type', $paymentTypes);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('userInfo', function ($sub) use ($search) {
                        $sub->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('payment_type', 'LIKE', "%{$search}%");
                });
            }

            if (!empty($paymentStatuses)) {
                $query->whereIn('payment_status', $paymentStatuses);
            }

            if (!empty($bookingBy)) {
                $query->where('booking_by', $bookingBy);
            }

            switch ($sortby) {
                case 'asc':
                    $query->orderBy('id', 'asc');
                    break;

                case 'desc':
                    $query->orderBy('id', 'desc');
                    break;

                case 'last_month':
                    $query->whereBetween('created_at', [
                        now()->subMonth()->startOfMonth(),
                        now()->subMonth()->endOfMonth(),
                    ])->orderBy('created_at', 'desc');
                    break;

                case 'last_7_days':
                    $query->where('created_at', '>=', now()->subDays(7))
                          ->orderBy('created_at', 'desc');
                    break;

                case 'latest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $bookings = $query->get();

            $data = $bookings->map(function ($booking) {
                $userInfo = $booking->userInfo;

                return [
                    'id' => $booking->reservation_id,
                    'name' => $userInfo ? "{$userInfo->first_name} {$userInfo->last_name}" : "N/A",
                    'amount' => $booking->final_price,
                    'payment_type' => ucfirst(str_replace('_', ' ', $booking->payment_type)),
                    'created_at' => $booking->created_at->format('d M Y'),
                    'payment_status' => $booking->payment_status,
                ];
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

}
