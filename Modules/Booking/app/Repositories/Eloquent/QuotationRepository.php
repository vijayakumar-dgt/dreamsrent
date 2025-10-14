<?php

namespace Modules\Booking\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingDetail;
use Modules\Booking\Models\BookingHistory;
use Modules\Booking\Repositories\Contracts\QuotationRepositoryInterface;
use Modules\Booking\Repositories\Eloquent\Support\QuotationBookingDataBuilder;
use Modules\Booking\Repositories\Eloquent\Support\QuotationBookingFormatter;
use Modules\Booking\Repositories\Eloquent\Support\QuotationBookingPersistence;
use Modules\Booking\Repositories\Eloquent\Support\QuotationBookingQueryBuilder;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\PricingType;

class QuotationRepository implements QuotationRepositoryInterface
{
    public const VEHICLE_NAME_SELECT      = 'vehicle_info.name as vehicle_name';
    public const CUSTOMER_FULLNAME_SELECT = "CONCAT(user_details.first_name, ' ', user_details.last_name) as customer_full_name";
    public const CUSTOMER_IMAGE_SELECT    = 'user_details.profile_image as customer_image';
    public const PICKUP_LOCATION_SELECT   = 'locations as pickup_location';
    public const DROP_LOCATION_SELECT     = 'locations as drop_location';
    public const VEHICLE_IMAGE_PATH       = 'vehicles/images/small/';
    public const STORAGE_PATH             = 'storage/';

    public function __construct(
        private readonly QuotationBookingDataBuilder $dataBuilder,
        private readonly QuotationBookingPersistence $persistence,
        private readonly QuotationBookingQueryBuilder $queryBuilder,
        private readonly QuotationBookingFormatter $formatter,
    ) {
    }

    public function create(): array
    {
        $auth = currentUser();
        $locations = Location::where('status', 1)
            ->where('language_id', $auth->language_id)
            ->get();

        $priceTypes = PricingType::where('type', 1)->get();
        $drivingTypes = DB::table('driving_types')->get();

        /** @var \Illuminate\Support\Collection<int, \stdClass> $customers */
        $customers = User::select(
            'users.id',
            'users.name as username',
            DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as full_name"),
        )
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->where(['users.user_type' => 3, 'users.status' => 1])
            ->get()
            ->map(function ($customer) {
                $customer->full_name = $customer->full_name ?? $customer->username;
                return $customer;
            });

        return [
            'locations'    => $locations,
            'priceTypes'   => $priceTypes,
            'drivingTypes' => $drivingTypes,
            'customers'    => $customers,
        ];
    }

    public function store(Request $request): array
    {
        $bookingId = $request->input('booking_id');

        $successMsg = !empty($bookingId)
            ? __('admin.bookings.reservation_create_success')
            : __('admin.bookings.reservation_update_success');
        $errorMsg = !empty($bookingId)
            ? __('admin.common.default_create_error')
            : __('admin.common.default_update_error');

        try {
            DB::beginTransaction();

            [$startDateTime, $endDateTime] = $this->dataBuilder->parseDateTimes($request);
            $data = $this->dataBuilder->prepareBookingData($request, $startDateTime, $endDateTime);
            $details = $this->dataBuilder->prepareBookingDetails($request);

            if (empty($bookingId)) {
                $booking = $this->persistence->createBooking($data, $details);
                $bookingId = $booking->id;
            } else {
                $this->persistence->updateBooking($bookingId, $data, $details);
            }

            DB::commit();

            $encryptedId = (is_int($bookingId) || is_string($bookingId))
                ? customEncrypt($bookingId, Booking::$reservationSecretKey)
                : null;

            return [
                'code'             => 200,
                'message'          => $successMsg,
                'view_details_url' => route('quotations.details', ['id' => $encryptedId]),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'code'    => 500,
                'message' => $errorMsg,
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function edit(Request $request, string|int|null $id): array
    {
        $locations = Location::where('status', 1)->get();
        $priceTypes = PricingType::where('type', 1)->get();
        $drivingTypes = DB::table('driving_types')->get();

        $customers = User::select(
            'users.id',
            DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as full_name"),
        )
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->where(['users.user_type' => 3, 'users.status' => 1])
            ->get();

        $bookingId = customDecrypt(($id ?? ''), Booking::$reservationSecretKey);

        $booking = Booking::select(
            'base_km',
            'km_extra_price',
            'expenses',
            'delivery_price',
            'tax_val',
            'tax_type'
        )->findOrFail($bookingId);

        return [
            'locations'    => $locations,
            'priceTypes'   => $priceTypes,
            'drivingTypes' => $drivingTypes,
            'customers'    => $customers,
            'bookingId'    => $bookingId,
            'booking'      => $booking,
        ];
    }

    public function bookingList(Request $request): array
    {
        try {
            $query = $this->queryBuilder->baseBookingQuery();

            $this->queryBuilder->applyFilters($query, $request);
            $this->queryBuilder->applySorting($query, $request);

            $totalRecords = Booking::join('users', 'users.id', '=', 'bookings.customer_id')
                ->where('bookings.booking_by', 'quotation')
                ->count();

            $filteredRecords = $query->count();

            $bookings = $query->offset((int) $request->start)
                ->limit((int) $request->length)
                ->get()
                ->map(fn ($booking) => $this->formatter->formatListBooking($booking));

            return [
                'draw'            => intval($request->input('draw', 0)),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data'            => $bookings,
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function getBookingDetails(Request $request): array
    {
        try {
            $id = $request->booking_id ?? '';

            if (empty($id)) {
                return [
                    'status'  => 'error',
                    'code'    => 400,
                    'message' => 'Booking id is required.',
                ];
            }

            $booking = $this->queryBuilder->findBookingForModal((int) $id);

            if (!empty($booking)) {
                $this->formatter->formatBasicDetails($booking);
            }

            return [
                'code'    => 200,
                'message' => 'Success',
                'data'    => $booking,
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function reservationViewDetails(Request $request, string|int|null $id): array
    {
        $bookingId = customDecrypt($id, Booking::$reservationSecretKey);

        $booking = $this->queryBuilder->findBookingWithDetails((int) $bookingId);

        if ($booking) {
            $this->formatter->formatDetailedBooking($booking);
        }

        $bookingHistories = BookingHistory::where('booking_id', $bookingId)
            ->get(['id', 'booking_id', 'created_at', 'message']);

        return [
            'bookingHistories' => $bookingHistories,
            'booking'          => $booking,
        ];
    }

    public function delete(Request $request): array
    {
        try {
            $id = $request->id;
            Booking::where('id', $id)->delete();
            BookingDetail::where('booking_id', $id)->delete();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.bookings.quotation_delete_success'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
            ];
        }
    }
}
