<?php

namespace Modules\Booking\Repositories\Eloquent;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingDetail;
use Modules\Booking\Models\BookingHistory;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\ExtraService;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\PricingType;
use Modules\CarInfo\Models\VehicleSeason;
use Modules\CarInfo\Models\VehicleTarrif;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\Booking\Repositories\Contracts\QuotationRepositoryInterface;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\InsuranceBenefit;

class QuotationRepository implements QuotationRepositoryInterface
{
    public function create(): array
    {
        $auth = current_user();
        $locations = Location::where('status', 1)->where('language_id', $auth->language_id)->get();
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
            ->get()->map(function ($customer) {
                $customer->full_name = $customer->full_name ?? $customer->username;
                return $customer;
            });

        $data = ['locations' => $locations, 'priceTypes' => $priceTypes, 'drivingTypes' => $drivingTypes, 'customers' => $customers];

        return $data;
    }

    public function store(Request $request): array
    {
        $bookingId = $request->input('booking_id'); // or wherever the booking ID comes from

        $successMsg = !empty($bookingId) ? __('admin.bookings.reservation_create_success') : __('admin.bookings.reservation_update_success');
        $errorMsg = !empty($bookingId) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            DB::beginTransaction();
            $startDate = $request->input('start_date');
            $startTime = $request->input('start_time');
            $endDate = $request->input('end_date');
            $endTime = $request->input('end_time');

            if (!is_string($startDate) || !is_string($startTime) || !is_string($endDate) || !is_string($endTime)) {
                throw new \InvalidArgumentException('Invalid date or time input.');
            }

            $startDateTime = Carbon::parse($startDate . ' ' . $startTime)->format('Y-m-d H:i:s');
            $endDateTime   = Carbon::parse($endDate . ' ' . $endTime)->format('Y-m-d H:i:s');


            $bookingId = $request->booking_id ?? null;

            $data = [
                'vehicle_id' => $request->vehicle_id,
                'customer_id' => $request->customer_id,
                "booking_by" => "quotation",
                'driver_id' => $request->driver_id ?? null,
                'driver_price' => $request->driver_price ?? 0,
                'vehicle_price' => $request->vehicle_price,
                'total_insurance_price' => $request->total_insurance_price ?? 0,
                'total_extra_service_price' => $request->total_extra_service_price ?? 0,
                'final_price' => $request->final_price ?? 0,
                'extra_service' => $request->extra_service ?? null,
                'insurance' => $request->insurance ?? null,
                'security_deposit' => $request->security_deposit ?? null,
                'start_datetime' => $startDateTime,
                'end_datetime' => $endDateTime,
                'pickup_location' => $request->pickup_location,
                'return_location' => $request->return_location,
                'booking_status' => 1,
                'booking_tariff' => $request->tariff ?? null,
                'driving_type' => $request->driving_type ?? null,
                'rental_type' => $request->vehicle_price_type ?? null,
                'no_of_passengers' => $request->no_of_passengers ?? null,
                'no_of_days' => $request->no_of_days ?? null,
                'vehicle_total_price' => $request->vehicle_total_price ?? null,
                'base_km' => $request->base_km ?? null,
                'km_extra_price' => $request->km_extra_price ?? null,
                'expenses' => $request->expenses ?? null,
                'delivery_price' => $request->delivery_price ?? null,
                'tax_val' => $request->tax_val ?? null,
                'tax_type' => $request->tax_type ?? null,
                'booking_date' => now(),
            ];
            $details = [
                'vehicle_price_type' => $request->vehicle_price_type,
                'vehicle_season_id' => $request->vehicle_season_id ?? null,
                'vehicle_tariff_id' => $request->vehicle_tariff_id ?? null,
            ];
            $vehicleTariff = '';
            $vehicleSeason = '';

            if ($request->vehicle_tariff_id) {
                $vehicleTariff = VehicleTarrif::find($request->vehicle_tariff_id);

                if ($vehicleTariff instanceof \Modules\CarInfo\Models\VehicleTarrif) {
                    $details['tariff_title']        = $vehicleTariff->tariff_title;
                    $details['tariff_price']        = $vehicleTariff->tariff_daily_price;
                    $details['tariff_from_days']    = $vehicleTariff->tariff_from_days;
                    $details['tariff_to_days']      = $vehicleTariff->tariff_to_days;
                    $details['tariff_base_km']      = $vehicleTariff->tariff_base_km;
                    $details['tariff_extra_price']  = $vehicleTariff->tariff_extra_price;
                }
            }
            if ($request->vehicle_season_id) {
                $vehicleSeason = VehicleSeason::find($request->vehicle_season_id);

                if ($vehicleSeason instanceof \Modules\CarInfo\Models\VehicleSeason) {
                    $details['seasonal_title']         = $vehicleSeason->seasonal_title;
                    $details['seasonal_start_date']    = $vehicleSeason->seasonal_start_date;
                    $details['seasonal_end_date']      = $vehicleSeason->seasonal_end_date;
                    $details['seasonal_daily_rate']    = $vehicleSeason->seasonal_daily_rate;
                    $details['seasonal_weekly_rate']   = $vehicleSeason->seasonal_weekly_rate;
                    $details['seasonal_monthly_rate']  = $vehicleSeason->seasonal_monthly_rate;
                    $details['seasonal_late_fee']      = $vehicleSeason->seasonal_late_fee;
                }
            }

            if (empty($bookingId)) {
                $data['created_by'] = Auth::guard('admin')->id();
                $booking = Booking::create($data);
                $bookingNumber = str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
                $bookingPrefix = GeneralSetting::select('value')->where('key', 'reservation_prefix')->first();
                $bookingPrefix = $bookingPrefix->value ?? 'RES';
                $booking->update(['reservation_id' => $bookingPrefix . $bookingNumber]);
                $details['booking_id'] = $booking->id;
                $bookingId = $booking->id;

                $bookingDetail = BookingDetail::create($details);

                $historyData = [
                    'bookings'         => $booking->toArray(),
                    'booking_details'  => $bookingDetail->toArray(),
                ];
                BookingHistory::create([
                    'booking_id' => $booking->id,
                    'action'     => 'create',
                    'data'       => json_encode($historyData),
                    'message'    => 'Quotations created'
                ]);

                $customer = User::where('id', $booking->customer_id)->first();
                $vehicle = VehicleInfo::where('id', $booking->vehicle_id)->first();
                $driver  = Driver::find($booking->driver_id);
                $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
                $notifyData = [
                    'user_name' => $customer->name ?? '',
                    'company_name' => $companyName,
                    'email'     => $customer->email ?? '',
                    'phonenumber' => $customer->phone_number ?? '',
                    'vehicle_name' => $vehicle->name ?? "",
                    'driver_name'  => $driver ? $driver->driver_name : "",
                    'reservation_id' => $booking->reservation_id ?? "",
                    'start_date'     => $booking->start_datetime ? formatDateTime($booking->start_datetime) : "",
                    'end_date'       => $booking->end_datetime ? formatDateTime($booking->end_datetime) : "",
                    'pickup_location' => $booking->pickupLocation ? $booking->pickupLocation->name : "",
                    'delivery_type'   => $booking->delivery_type ?? "",
                    'rental_type'     => $booking->rental_type ?? "",
                    'payment_type'    => $booking->payment_type ?? "",
                    'payment_status'  => $booking->payment_status ?? "",
                    'tototal_amount'  => $booking->final_price ?? ""
                ];

                try {
                    if (rentalNotificationEnabled()) {
                        $appAdmin = User::where('user_type', 1)->first();
                        if ($appAdmin !== null) {
                            sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);
                        }

                        if ($customer !== null) {
                            sendNotification($customer->email, 'booking-confirmation-to-user', $notifyData);
                        }
                    }
                } catch (\Exception $e) {
                }
            } else {
                $data['updated_by'] = Auth::guard('admin')->id();

                Booking::where('id', $bookingId)->update($data);
                BookingDetail::where('booking_id', $bookingId)->update($details);

                $booking = Booking::find($bookingId);
                $bookingDetail = BookingDetail::where('booking_id', $bookingId)->first();

                $historyData = [
                    'bookings' => $booking instanceof \Modules\Booking\Models\Booking ? $booking->toArray() : [],
                    'booking_details' => $bookingDetail instanceof \Modules\Booking\Models\BookingDetail ? $bookingDetail->toArray() : [],
                ];
                if ($booking instanceof \Modules\Booking\Models\Booking) {
                    BookingHistory::create([
                        'booking_id' => $booking->id,
                        'action'     => 'update',
                        'data'       => json_encode($historyData),
                        'message'    => 'Quotations updated'
                    ]);
                }
            }

            DB::commit();

            $encryptedId = (is_int($bookingId) || is_string($bookingId)) ? customEncrypt($bookingId, Booking::$reservationSecretKey) : null;

            $response = [
                'code' => 200,
                'message' => $successMsg,
                'view_details_url' => route('quotations.details', ['id' => $encryptedId]),
            ];

            return $response;
        } catch (\Exception $e) {
            DB::rollBack();

            $response = [
                'code' => 500,
                'message' => $errorMsg,
                'error' => $e->getMessage(),
            ];
            return $response;
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
        )->findOrFail($bookingId); // Assuming you have $bookingId

        $data = ['locations' => $locations, 'priceTypes' => $priceTypes, 'drivingTypes' => $drivingTypes, 'customers' => $customers, 'bookingId' => $bookingId, 'booking' => $booking];

        return $data;
    }

    public function bookingList(Request $request): array
    {
        try {
            $query = Booking::select(
                'bookings.id',
                'bookings.reservation_id',
                'bookings.booking_date',
                'vehicle_info.name as vehicle_name',
                'vehicle_info.vehicle_image',
                DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as customer_full_name"),
                'user_details.profile_image as customer_image',
                'users.name as user_name',
                'bookings.start_datetime',
                'bookings.end_datetime',
                'pickup_location.name as pickup_location',
                'drop_location.name as drop_location',
                'bookings.booking_status',
                'bookings.booking_by',
            )
                ->join('users', 'users.id', '=', 'bookings.customer_id')
                ->leftJoin('user_details', 'user_details.user_id', '=', 'users.id')
                ->join('locations as pickup_location', 'pickup_location.id', '=', 'bookings.pickup_location')
                ->join('locations as drop_location', 'drop_location.id', '=', 'bookings.return_location')
                ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
                ->where('bookings.booking_by', '=', 'quotation');

            //  DataTables Search
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $search = (string) $search;
                    $q->where('bookings.reservation_id', 'LIKE', "%{$search}%")
                        ->orWhere('vehicle_info.name', 'LIKE', "%{$search}%")
                        ->orWhere('users.name', 'LIKE', "%{$search}%");
                });
            }

            // Status Filter
            if ($request->has('status') && !empty($request->status)) {
                $query->whereIn('bookings.booking_status', $request->status);
            }

            if ($request->has('pickup_location_ids') && !empty($request->pickup_location_ids)) {
                $query->whereIn('bookings.pickup_location', $request->pickup_location_ids);
            }

            // Apply Drop Location Filter
            if ($request->has('drop_location_ids') && !empty($request->drop_location_ids)) {
                $query->whereIn('bookings.return_location', $request->drop_location_ids);
            }

            // Date Filter
            if ($request->has('sort_by_date') && !empty($request->sort_by_date)) {
                $sortByDate = is_string($request->sort_by_date) ? $request->sort_by_date : '';
                $dates = explode(' - ', $sortByDate);
                if (count($dates) === 2) {
                    $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]));
                    $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]));

                    // Apply date filter only if valid date format
                    if ($startDate && $endDate) {
                        $query->whereBetween('bookings.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
                    }
                }
            }

            // Apply Sort Filter
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch (strtolower((string) ($request->sort_by ?? ''))) {
                    case 'latest':
                        $query->orderBy('bookings.created_at', 'desc');
                        break;
                    case 'ascending':
                        $query->orderBy('bookings.reservation_id', 'asc');
                        break;
                    case 'descending':
                        $query->orderBy('bookings.reservation_id', 'desc');
                        break;
                    case 'last month':
                        $startDate = \Carbon\Carbon::now()->subMonth()->startOfMonth();
                        $endDate = \Carbon\Carbon::now()->subMonth()->endOfMonth();
                        $query->whereBetween('bookings.created_at', [$startDate, $endDate]);
                        break;
                    case 'last 7 days':
                        $startDate = \Carbon\Carbon::now()->subDays(7)->startOfDay();
                        $endDate = \Carbon\Carbon::now()->endOfDay();
                        $query->whereBetween('bookings.created_at', [$startDate, $endDate]);
                        break;
                }
            }

            // Sorting
            $columns = ['id', 'reservation_id', 'vehicle_name', 'customer_full_name', 'pickup_location', 'drop_location', 'booking_status'];
            $orderBy = $columns[$request->input('order.0.column', 0)] ?? 'id';
            $orderDir = $request->input('order.0.dir', 'desc');
            $query->orderBy($orderBy, $orderDir);

            // Pagination
            $totalRecords = Booking::join('users', 'users.id', '=', 'bookings.customer_id')
                ->where('bookings.booking_by', '=', 'quotation')
                ->count();
            $filteredRecords = $query->count();

            $query->offset((int) $request->start)->limit((int) $request->length);
            $bookings = $query->get();

            // Format Response Data
            $bookings->map(function ($booking) {
                $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
                $booking->vehicle_image = uploadedAsset($booking->vehicle_image);
                $booking->booking_status_text = Booking::getStatusLabel((int) $booking->booking_status);

                return $booking;
            });

            $response = [
                "draw" => intval($request->input('draw', 0)),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $bookings
            ];
            return $response;
        } catch (\Exception $e) {
            $response = [
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ];
            return $response;
        }
    }

    public function getBookingDetails(Request $request): array
    {
        try {
            $id = $request->booking_id ?? '';

            if (empty($id)) {
                $response = [
                    'status' => 'error',
                    'code'   => 400,
                    'message' => 'Booking id is required.'
                ];
                return $response;
            }

            $booking = Booking::select(
                'bookings.*',
                'vehicle_info.name as vehicle_name',
                'vehicle_info.vehicle_image',
                DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as customer_full_name"),
                'user_details.profile_image as customer_image',
                'users.name as user_name',
                'pickup_location.name as pickup_location_name',
                'drop_location.name as drop_location_name',
            )
                ->join('users', 'users.id', '=', 'bookings.customer_id')
                ->join('user_details', 'user_details.user_id', '=', 'users.id')
                ->join('locations as pickup_location', 'pickup_location.id', '=', 'bookings.pickup_location')
                ->join('locations as drop_location', 'drop_location.id', '=', 'bookings.return_location')
                ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
                ->where('bookings.id', $id)
                ->first();

            if (!empty($booking)) {
                $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
                $vehicleImagePath = $booking->vehicle_image ?? '';
                $filename = basename($vehicleImagePath);
                $newpath = 'vehicles/images/small/' . $filename;
                $file = public_path('storage/' . $newpath);
                if (file_exists($file)) {
                    $vehicleImagePath = $newpath;
                }
                $booking->vehicle_image = uploadedAsset($vehicleImagePath);
                if (!empty($booking->insurance)) {
                    $booking->insurance_formatted = json_decode($booking->insurance, true);
                }

                if ($booking->extra_service) {
                    $booking->extra_service_formatted = json_decode($booking->extra_service, true);
                }
                $booking->booking_status_text = Booking::getStatusLabel((int) $booking->booking_status);
            }

            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $booking,
            ];
            return $response;
        } catch (\Exception $e) {
            $response = [
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ];
            return $response;
        }
    }

    public function reservationViewDetails(Request $request, string|int|null $id): array
    {
        $bookingId = customDecrypt($id, Booking::$reservationSecretKey);

        $booking = Booking::select(
            'bookings.id',
            'bookings.reservation_id',
            'bookings.vehicle_id',
            'bookings.booking_status',
            'bookings.booking_date',
            'bookings.start_datetime',
            'bookings.end_datetime',
            'bookings.no_of_days',
            'bookings.driving_type',
            'bookings.pickup_location',
            'bookings.return_location',
            'bookings.security_deposit',
            'bookings.customer_id',
            'bookings.driver_id',
            'bookings.insurance',
            'bookings.extra_service',
            'bookings.driver_price',
            'bookings.vehicle_price',
            'bookings.vehicle_total_price',
            'bookings.total_insurance_price',
            'bookings.total_extra_service_price',
            'bookings.final_price',
            'vehicle_info.name as vehicle_name',
            'vehicle_info.vehicle_image',
            'cartypes.name as vehicle_type',
            'pickup_location.name as pickup_location_name',
            'drop_location.name as drop_location_name',
            DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as customer_full_name"),
            'user_details.profile_image as customer_image',
            'users.name as customer_user_name',
            'users.phone_number as customer_phone_number',
            'drivers.driver_name',
            'drivers.image as driver_image',
            'drivers.phone_number as driver_phone_number',
            'booking_details.vehicle_price_type',
            'bookings.rental_type',
            'bookings.delivery_type',
            'bookings.booking_by',
            'driving_types.name as driving_type_name',
        )
            ->leftjoin('booking_details', 'booking_details.booking_id', '=', 'bookings.id')
            ->join('users', 'users.id', '=', 'bookings.customer_id')
            ->leftJoin('user_details', 'user_details.user_id', '=', 'users.id')
            ->join('locations as pickup_location', 'pickup_location.id', '=', 'bookings.pickup_location')
            ->join('locations as drop_location', 'drop_location.id', '=', 'bookings.return_location')
            ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
            ->leftJoin('cartypes', 'cartypes.id', '=', 'vehicle_info.type_id')
            ->leftjoin('drivers', 'drivers.id', '=', 'bookings.driver_id')
            ->leftjoin('driving_types', 'driving_types.id', '=', 'bookings.driving_type')
            ->where('bookings.id', $bookingId)
            ->firstOrFail();

        if ($booking) {
            $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
            $booking->driver_image = uploadedAsset($booking->driver_image, 'profile');
            $booking->vehicle_image = uploadedAsset($booking->vehicle_image);

            $booking->extra_service_count = 0;
            $booking->extra_service_names = [];

            if (!empty($booking->extra_service)) {
                $extraServiceArray = json_decode($booking->extra_service, true);
                if (is_array($extraServiceArray)) {
                    $booking->extra_service_formatted = $extraServiceArray;
                    $booking->extra_service_count = count($extraServiceArray);
                    $extraServiceIds = collect($extraServiceArray)->pluck('id')->toArray();
                    $booking->extra_service_names = ExtraService::whereIn('id', $extraServiceIds)->pluck('name')->toArray();
                }
            }

            $booking->insurance_count = 0;
            $booking->insurance_benefits_formatted = [];
            $booking->insurance_names = [];

            if (!empty($booking->insurance)) {
                $insuranceArray = json_decode($booking->insurance, true);
                if (is_array($insuranceArray)) {
                    $booking->insurance_formatted = $insuranceArray;
                    $booking->insurance_count = count($insuranceArray);
                    $insuranceIds = collect($insuranceArray)->pluck('id')->toArray();
                    $booking->insurance_names = Insurance::whereIn('id', $insuranceIds)->pluck('insurance_name')->toArray();
                    $booking->insurance_benefits_formatted = InsuranceBenefit::whereIn('insurance_id', $insuranceIds)->pluck('benefit')->toArray();
                }
            }

            $status = is_numeric($booking->booking_status) ? (int)$booking->booking_status : 4;
            $booking->booking_status_text = Booking::getStatusLabel($status);
            $booking->currency_symbol = getDefaultCurrencySymbol();

            if ($booking->delivery_type) {
                $booking->delivery_type = $booking->delivery_type == "self_pickup" ? 'Self Pickup' : 'Delivery';
            }

            $booking->driver_price = number_format((float) ($booking->driver_price ?? 0), 2, '.', '');
            $booking->vehicle_price = number_format((float) ($booking->vehicle_price ?? 0), 2, '.', '');
            $booking->vehicle_total_price = number_format((float) ($booking->vehicle_total_price ?? 0), 2, '.', '');
            $booking->total_insurance_price = number_format((float) ($booking->total_insurance_price ?? 0), 2, '.', '');
            $booking->total_extra_service_price = number_format((float) ($booking->total_extra_service_price ?? 0), 2, '.', '');
            $booking->final_price = number_format((float) ($booking->final_price ?? 0), 2, '.', '');
        }

        $bookingHistories = BookingHistory::where('booking_id', $bookingId)->get([
            'id',
            'booking_id',
            'created_at',
            'message',
        ]);

        $data = ['bookingHistories' => $bookingHistories, 'booking' => $booking];

        return $data;
    }

    public function delete(Request $request): array
    {
        try {
            $id = $request->id;
            Booking::where('id', $id)->delete();
            BookingDetail::where('booking_id', $id)->delete();

            $response = [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.bookings.quotation_delete_success')
            ];
            return $response;
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error'),
            ];
            return $response;
        }
    }
}
