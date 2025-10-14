<?php

namespace Modules\Booking\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingDetail;
use Modules\Booking\Repositories\Contracts\BookingRepositoryInterface;
use Modules\Booking\Repositories\Eloquent\Support\BookingCancellationService;
use Modules\Booking\Repositories\Eloquent\Support\BookingDetailService;
use Modules\Booking\Repositories\Eloquent\Support\BookingListService;
use Modules\Booking\Repositories\Eloquent\Support\BookingPersistenceService;
use Modules\Booking\Repositories\Eloquent\Support\BookingPriceCalculator;
use Modules\Booking\Repositories\Eloquent\Support\BookingQueryConfig;
use Modules\Booking\Repositories\Eloquent\Support\VehicleFilterService;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\PricingType;

class BookingRepository implements BookingRepositoryInterface
{
    public function __construct(
        private readonly VehicleFilterService $vehicleFilterService,
        private readonly BookingPersistenceService $bookingPersistenceService,
        private readonly BookingListService $bookingListService,
        private readonly BookingDetailService $bookingDetailService,
        private readonly BookingPriceCalculator $bookingPriceCalculator,
        private readonly BookingCancellationService $bookingCancellationService,
    ) {
    }

    public function create(): array
    {
        $locations = Location::where('status', 1)->get();
        $priceTypes = PricingType::where('type', 1)->get();
        $drivingTypes = DB::table('driving_types')->get();

        $customers = User::select(
            'users.id',
            BookingQueryConfig::USERNAME_SELECT,
            DB::raw(BookingQueryConfig::FULLNAME_SELECT),
        )
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->where(['users.user_type' => 3, 'users.status' => 1])
            ->get()
            ->map(function ($customer) {
                $customer->full_name = ucwords($customer->full_name) ?? $customer->username;
                return $customer;
            });

        return [
            'locations'    => $locations,
            'priceTypes'   => $priceTypes,
            'drivingTypes' => $drivingTypes,
            'customers'    => $customers,
        ];
    }

    public function getCustomerDetails(Request $request): array
    {
        try {
            $customerId = $request->customer_id ?? '';
            $customer = User::select(
                'users.id',
                BookingQueryConfig::USERNAME_SELECT,
                DB::raw(BookingQueryConfig::FULLNAME_SELECT),
                DB::raw("(SELECT COUNT(*) FROM bookings WHERE bookings.customer_id = users.id) as bookings_count"),
                'users.email',
                'users.phone_number',
                'user_details.profile_image'
            )
                ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                ->where(['users.user_type' => 3, 'users.status' => 1, 'users.id' => $customerId])
                ->first();

            if ($customer) {
                $customer->full_name = ucwords($customer->full_name) ?? $customer->username;
                $customer->profile_image = uploadedAsset(
                    is_string($customer->profile_image) ? $customer->profile_image : null,
                    'profile'
                );
            }

            return [
                'code'    => 200,
                'message' => 'Customer retrieved successfully.',
                'data'    => $customer,
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function getFilterVehicles(Request $request): array
    {
        try {
            $vehicles = $this->vehicleFilterService->filterVehicles(
                $request,
                BookingQueryConfig::VEHICLE_IMAGE_PATH,
                BookingQueryConfig::STORAGE_PATH
            );

            return [
                'code'    => 200,
                'message' => __('Vehicles retrieved successfully.'),
                'data'    => $vehicles,
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function store(Request $request): array
    {
        $bookingId = $request->booking_id ?? null;
        $successMsg = empty($bookingId)
            ? __('admin.bookings.reservation_create_success')
            : __('admin.bookings.reservation_update_success');
        $errorMsg = empty($bookingId)
            ? __('admin.common.default_create_error')
            : __('admin.common.default_update_error');

        try {
            DB::beginTransaction();

            [$startDateTime, $endDateTime] = $this->bookingPersistenceService->parseBookingDateTimes(
                $request->start_date ?? '',
                $request->start_time ?? '',
                $request->end_date ?? '',
                $request->end_time ?? '',
                BookingQueryConfig::DISPLAY_DATE_FORMAT,
                BookingQueryConfig::DB_DATE_FORMAT
            );

            $payload = $request->all();
            $data = $this->bookingPersistenceService->prepareBookingData($payload, $startDateTime, $endDateTime);
            $details = $this->bookingPersistenceService->prepareBookingDetails($payload);

            if (empty($bookingId)) {
                $booking = $this->bookingPersistenceService->createBooking($data, $details);
                $this->bookingPersistenceService->sendBookingNotifications($booking, BookingQueryConfig::DEFAULT_COMPANY_NAME);
            } else {
                $booking = $this->bookingPersistenceService->updateBooking((int) $bookingId, $data, $details);
            }

            DB::commit();

            $encryptedId = customEncrypt($booking->id, Booking::$reservationSecretKey);

            return [
                'code'            => 200,
                'message'         => $successMsg,
                'view_details_url'=> route('reservation.details', ['id' => $encryptedId]),
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
            BookingQueryConfig::USERNAME_SELECT,
            DB::raw(BookingQueryConfig::FULLNAME_SELECT),
        )
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->where(['users.user_type' => 3, 'users.status' => 1])
            ->get()
            ->map(function ($customer) {
                $customer->full_name = ucwords($customer->full_name) ?? $customer->username;
                return $customer;
            });

        $bookingId = customDecrypt($id, Booking::$reservationSecretKey);

        return [
            'locations'    => $locations,
            'priceTypes'   => $priceTypes,
            'drivingTypes' => $drivingTypes,
            'customers'    => $customers,
            'bookingId'    => $bookingId,
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
                'message' => __('admin.bookings.reservation_delete_success'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
            ];
        }
    }

    public function complete(Request $request): array
    {
        try {
            $id = $request->id;
            Booking::where('id', $id)->update(['booking_status' => 5]);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.bookings.reservation_complete_success'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
            ];
        }
    }

    public function bookingList(Request $request): array
    {
        try {
            $query = $this->bookingListService->buildBookingQuery($request);

            $totalRecords = $this->bookingListService->getTotalBookingCount();
            $filteredRecords = $query->count();

            $bookings = $query->offset($request->start)
                ->limit($request->length)
                ->get();

            $bookings = $this->bookingListService->formatBookingData($bookings);

            return [
                'draw'            => intval($request->draw),
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

            $booking = $this->bookingDetailService->fetchBookingDetails((int) $id);
            $this->bookingDetailService->formatBookingForDetails($booking);

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

        return $this->bookingDetailService->getReservationData((int) $bookingId);
    }

    public function calculateTotalPrice(Request $request): array
    {
        try {
            $response = $this->bookingPriceCalculator->calculate($request);

            return [
                'code'    => 200,
                'message' => 'Success',
                'data'    => $response,
            ];
        } catch (\Throwable $e) {
            return [
                'code'    => 500,
                'message' => 'An error occurred while calculating the total price.',
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function cancelBooking(Request $request): array
    {
        try {
            $booking = Booking::find($request->booking_id);

            if (!$booking) {
                return [
                    'code'    => 404,
                    'message' => __('admin.bookings.not_found'),
                ];
            }

            $this->bookingCancellationService->cancel($booking, $request, BookingQueryConfig::DEFAULT_COMPANY_NAME);

            return [
                'code'         => 200,
                'message'      => __('admin.bookings.reservation_cancel_success'),
                'redirect_url' => route('reservation.index'),
            ];
        } catch (\Throwable $e) {
            return [
                'code'    => 500,
                'message' => __('admin.bookings.reservation_cancel_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }
}
