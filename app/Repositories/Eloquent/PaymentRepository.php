<?php

namespace App\Repositories\Eloquent;

use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingUserInfo;
use App\Repositories\Contracts\PaymentInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository implements PaymentInterface
{
    public function getDistinctPaymentTypes(): array
    {
        return Booking::select("payment_type")->distinct()->pluck("payment_type")->toArray();
    }

    public function getPaymentList(array $params): array
    {
        $start = $params['start'] ?? 0;
        $length = $params['length'] ?? 10;
        $search = $params['search'] ?? null;
        $sortColumn = $params['sort_column'] ?? 'id';
        $sortDirection = $params['sort_direction'] ?? 'desc';
        $paymentStatuses = $params['payment_status'] ?? [];
        $paymentTypes = $params['payment_type'] ?? [];
        $sortBy = $params['sortby'] ?? null;

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

        $this->applySorting($query, $sortBy);

        $totalData = $query->count();

        $query->orderBy($sortColumn === 'name' ? 'id' : $sortColumn, $sortDirection);

        $bookings = $query->skip($start)->take($length)->get();

        return [
            'data' => $this->formatPaymentData($bookings),
            'total' => $totalData,
            'filtered' => $totalData,
        ];
    }

    public function formatPaymentData(Collection $bookings): array
    {
        return $bookings->map(function (Booking $booking): array {
            $userInfo = $booking->userInfo;
            $createdAt = $booking->created_at;
            $paymentType = $this->formatPaymentType($booking->payment_type);

            return [
                'id' => $booking->reservation_id,
                'name' => $userInfo ? ucfirst($userInfo->first_name) . " " . ucfirst($userInfo->last_name) : "-",
                'profile_image' => $booking->customerDetail->profile_image ? uploadedAsset($booking->customerDetail->profile_image) : uploadedAsset('', 'profile'),
                'amount' => $booking->final_price,
                'payment_type' => $paymentType,
                'created_at' => formatDateTime($createdAt, false),
                'payment_status' => $booking->payment_status,
            ];
        })->toArray();
    }

    protected function applySorting($query, ?string $sortBy): void
    {
        if (!$sortBy) return;

        switch (strtolower($sortBy)) {
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
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                $query->whereBetween('created_at', [$startDate, $endDate]);
                break;
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(7)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
                break;
        }
    }

    protected function formatPaymentType(string $paymentType): string
    {
        return $paymentType == 'cod' 
            ? strtoupper(str_replace('_', ' ', $paymentType))
            : ucfirst(str_replace('_', ' ', $paymentType));
    }
}