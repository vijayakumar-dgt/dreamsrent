<?php

namespace Modules\Booking\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingDetail;
use Modules\Booking\Models\BookingHistory;
use Modules\Booking\Repositories\Contracts\BookingRepositoryInterface;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\ExtraService;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\PricingType;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Models\VehicleSeason;
use Modules\CarInfo\Models\VehicleTarrif;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\InsuranceBenefit;

class BookingRepository implements BookingRepositoryInterface
{
    private const USERNAME_SELECT = 'users.name as username';
    private const FULLNAME_SELECT = "CONCAT(user_details.first_name, ' ', user_details.last_name) as full_name";
    private const DISPLAY_DATE_FORMAT = 'd-m-Y H:i';
    private const DB_DATE_FORMAT = 'Y-m-d H:i:s';
    private const VEHICLE_NAME_SELECT = 'vehicle_info.name as vehicle_name';
    private const VEHICLE_IMAGE_PATH   = 'vehicles/images/small/';
    private const STORAGE_PATH         = 'storage/';
    private const DEFAULT_COMPANY_NAME = 'Default Company Name';

    private const CUSTOMER_IMAGE_SELECT     = 'user_details.profile_image as customer_image';
    private const CUSTOMER_FULLNAME_SELECT  = "CONCAT(user_details.first_name, ' ', user_details.last_name) as customer_full_name";
    private const PICKUP_LOCATION_SELECT    = 'locations as pickup_location';
    private const DROP_LOCATION_SELECT      = 'locations as drop_location';

    public function create(): array
    {
        $locations = Location::where('status', 1)->get();
        $priceTypes = PricingType::where('type', 1)->get();
        $drivingTypes = DB::table('driving_types')->get();

        /** @var \Illuminate\Support\Collection<int, \stdClass> $customers */
        $customers = User::select(
            'users.id',
            self::USERNAME_SELECT,
            DB::raw(self::FULLNAME_SELECT),
        )
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->where(['users.user_type' => 3, 'users.status' => 1])
            ->get()->map(function ($customer) {
                $customer->full_name = ucwords($customer->full_name) ?? $customer->username;
                return $customer;
            });

        return [
            'locations'   => $locations,
            'priceTypes'  => $priceTypes,
            'drivingTypes'=> $drivingTypes,
            'customers'   => $customers,
        ];
    }

    public function getCustomerDetails(Request $request): array
    {
        try {
            $customerId = $request->customer_id ?? '';
            $customer = User::select(
                'users.id',
                self::USERNAME_SELECT,
                DB::raw(self::FULLNAME_SELECT),
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
            $filters = $this->extractFilters($request);

            $vehicles = $this->buildVehicleQuery($filters)
                ->orderBy('vehicle_info.id', $filters['orderBy'])
                ->paginate($filters['perPage'], ['*'], 'page', $filters['page']);

            $vehicles->getCollection()->transform(fn($vehicle) => $this->formatVehicle($vehicle));

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

    private function extractFilters(Request $request): array
    {
        $filters = [
            'orderBy'       => $request->order_by ?? 'desc',
            'search'        => $request->search ?? null,
            'perPage'       => $request->per_page ?? 10,
            'page'          => $request->page ?? 1,
            'tariff'        => $request->tariff ?? null,
            'brandIds'      => $request->brand_ids ?? null,
            'modelIds'      => $request->model_ids ?? null,
            'typeIds'       => $request->type_ids ?? null,
            'colorIds'      => $request->color_ids ?? null,
            'pickupLocation'=> $request->pickup_location ?? null,
            'returnLocation'=> $request->return_location ?? null,
            'bookingId'     => $request->booking_id ?? null,
        ];

        // Handle dates & times
        [$filters['startDateTime'], $filters['startDateFormat']] = $this->parseDateTime(
            $request->start_date ?? null,
            $request->start_time ?? null
        );

        [$filters['endDateTime'], $filters['endDateFormat']] = $this->parseDateTime(
            $request->end_date ?? null,
            $request->end_time ?? null
        );

        return $filters;
    }

    private function parseDateTime(?string $date, ?string $time): array
    {
        if (empty($date) || !is_string($date)) {
            return ['', ''];
        }

        $dateTimeString = $date . ($time ? " {$time}" : '');
        $dateFormat = $time ? self::DISPLAY_DATE_FORMAT : 'd-m-Y';

        $carbon = Carbon::createFromFormat($dateFormat, $dateTimeString);
        $carbonOnly = Carbon::createFromFormat('d-m-Y', $date);

        return [
            $carbon ? $carbon->format(self::DB_DATE_FORMAT) : '',
            $carbonOnly ? $carbonOnly->format('Y-m-d') : ''
        ];
    }

    private function buildVehicleQuery(array $filters)
    {
        $query = VehicleInfo::select(
                'vehicle_info.id',
                'vehicle_info.vehicle_image as image',
                self::VEHICLE_NAME_SELECT,
                'vehicle_info.year',
                'cartypes.name as vehicle_type',
                'brands.brand_name',
                'car_models.model_name',
                'car_colors.name as color_name',
                'car_colors.value as color_value',
            )
                ->Join('cartypes', 'vehicle_info.type_id', '=', 'cartypes.id')
                ->Join('brands', 'vehicle_info.brand_id', '=', 'brands.id')
                ->Join('car_models', 'vehicle_info.model_id', '=', 'car_models.id')
                ->Join('car_colors', 'vehicle_info.color_id', '=', 'car_colors.id')
                ->leftJoin('bookings', 'vehicle_info.id', '=', 'bookings.vehicle_id')
                ->distinct()

                ->whereDoesntHave('maintenances', function ($query) use ($filters) {
                    $query->where(function ($q) use ($filters) {
                        $q->where('maintenances.start_date', '<=', $filters['endDateFormat'])
                            ->where('maintenances.end_date', '>=', $filters['startDateFormat'])
                            ->where('maintenances.status', '!=', 3);
                    });
                })

                ->when(!empty($brandIds), fn($query) => $query->whereIn('vehicle_info.brand_id', $filters['brandIds']))
                ->when(!empty($typeIds), fn($query) => $query->whereIn('vehicle_info.type_id', $filters['typeIds']))
                ->when(!empty($modelIds), fn($query) => $query->whereIn('vehicle_info.model_id', $filters['modelIds']))
                ->when(!empty($colorIds), fn($query) => $query->whereIn('vehicle_info.color_id', $filters['colorIds']))

                ->when(!empty($filters['pickupLocation']), function ($query) use ($filters) {
                    return $query->where(function ($q) use ($filters) {
                        $q->where('vehicle_info.main_location_id', '=', $filters['pickupLocation'])
                            ->orWhereJsonContains('vehicle_info.other_location_id', (string) $filters['pickupLocation']);
                    });
                })
                ->when(!empty($filters['returnLocation']), function ($query) use ($filters) {
                    return $query->where(function ($q) use ($filters) {
                        $q->where('vehicle_info.main_location_id', '=', $filters['returnLocation'])
                            ->orWhereJsonContains('vehicle_info.other_location_id', (string) $filters['returnLocation']);
                    });
                })

                ->when($filters['search'], function ($query) use ($filters) {

                    $search = (string) $filters['search'];
                    $tariff = (string) $filters['tariff'];
                    $path = '$[0].' . $filters['tariff'];

                    $query->where(function ($q) use ($search) {
                        $q->where('vehicle_info.year', 'LIKE', "%{$search}%")
                            ->orWhere('vehicle_info.name', 'LIKE', "%{$search}%")
                            ->orWhere('brands.brand_name', 'LIKE', "%{$search}%")
                            ->orWhere('car_models.model_name', 'LIKE', "%{$search}%")
                            ->orWhere('cartypes.name', 'LIKE', "%{$search}%")
                            ->orWhere('car_colors.name', 'LIKE', "%{$search}%");
                    });

                    if (filled($tariff)) {
                        $query->orWhereRaw(
                            "JSON_UNQUOTE(JSON_EXTRACT(vehicle_info.vehicle_price, ?)) LIKE ?",
                            [$path, "%{$search}%"]
                        );
                    }
                })

                ->when($filters['tariff'], function ($query) use ($filters) {
                    $noOfDays = 1;

                    if (!empty($filters['startDateTime']) && !empty($filters['endDateTime'])) {
                        $start = Carbon::parse($filters['startDateTime']);
                        $end = Carbon::parse($filters['endDateTime']);
                        $diffMinutes = $start->diffInMinutes($end);
                        $noOfDays = ceil($diffMinutes / 1440);
                    }

                    return $query->leftJoin('vehicle_tarrifs', function ($join) use ($noOfDays) {
                        $join->on('vehicle_tarrifs.vehicle_id', '=', 'vehicle_info.id')
                            ->whereRaw('CAST(vehicle_tarrifs.tariff_from_days AS UNSIGNED) <= ?', [$noOfDays])
                            ->whereRaw('CAST(vehicle_tarrifs.tariff_to_days AS UNSIGNED) >= ?', [$noOfDays]);
                    })
                        ->where(function ($q) use ($filters) {
                            $q->whereNotNull("vehicle_tarrifs.tariff_daily_price")
                                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(vehicle_info.vehicle_price, '$[0].{$filters["tariff"]}')) IS NOT NULL");
                        })
                        ->selectRaw("
                        vehicle_tarrifs.id as vehicle_tariff_id,
                        COALESCE(
                            vehicle_tarrifs.tariff_daily_price,
                            JSON_UNQUOTE(JSON_EXTRACT(vehicle_info.vehicle_price, '$[0].{$filters["tariff"]}'))
                        ) as vehicle_price,
                        ? as vehicle_price_type
                    ", [$filters['tariff']]);
                })

                ->when(empty($tariff), function ($query) use ($filters) {
                    $noOfDays = 1;

                    if (!empty($filters['startDateTime']) && !empty($filters['endDateTime'])) {
                        $start = Carbon::parse($filters['startDateTime']);
                        $end = Carbon::parse($filters['endDateTime']);
                        $diffMinutes = $start->diffInMinutes($end);
                        $noOfDays = ceil($diffMinutes / 1440);
                    }
                    $start = $filters['startDateTime'] ? Carbon::parse($filters['startDateTime']) : Carbon::now();
                    $daysInMonth = $start->daysInMonth;

                    $tariffType = 'daily';
                    $seasonalRateColumn = 'seasonal_daily_rate';

                    if ($noOfDays >= 7 && $noOfDays < $daysInMonth) {
                        $tariffType = 'weekly';
                        $seasonalRateColumn = 'seasonal_weekly_rate';
                    } elseif ($noOfDays >= $daysInMonth && $noOfDays < 365) {
                        $tariffType = 'monthly';
                        $seasonalRateColumn = 'seasonal_monthly_rate';
                    } elseif ($noOfDays >= 365) {
                        $tariffType = 'yearly';
                    }

                    $jsonPath = "$[0].$tariffType";

                    $end = $filters['endDateTime'] ? Carbon::parse($filters['endDateTime']) : Carbon::now();
                    return $query->leftJoin('vehicle_seasons', function ($join) use ($start, $end) {
                        $join->on('vehicle_seasons.vehicle_id', '=', 'vehicle_info.id')
                            ->where(function ($q) use ($start, $end) {
                                $q->whereDate('vehicle_seasons.seasonal_start_date', '<=', $start)
                                    ->whereDate('vehicle_seasons.seasonal_end_date', '>=', $end);
                            });
                    })
                        ->where(function ($q) use ($seasonalRateColumn, $jsonPath) {
                            $q->whereNotNull("vehicle_seasons.$seasonalRateColumn")
                                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(vehicle_info.vehicle_price, ?)) IS NOT NULL", [$jsonPath]);
                        })
                        ->selectRaw("
                        vehicle_seasons.id as vehicle_season_id,
                        COALESCE(
                            vehicle_seasons.$seasonalRateColumn,
                            JSON_UNQUOTE(JSON_EXTRACT(vehicle_info.vehicle_price, ?))
                        ) as vehicle_price,
                        COALESCE(
                            CASE
                                WHEN vehicle_seasons.$seasonalRateColumn IS NOT NULL THEN ?
                                ELSE ?
                            END, ?
                        ) as vehicle_price_type
                        ", [
                            $jsonPath,
                            $tariffType,
                            $tariffType,
                            $tariffType
                        ]);
                })

                ->when(!empty($filters['startDateTime']) || !empty($filters['endDateTime']), function ($query) use ($filters) {
                    $query->whereNotExists(function ($q) use ($filters) {
                        $q->select(DB::raw(1))
                            ->from('bookings')
                            ->whereRaw('bookings.vehicle_id = vehicle_info.id')
                            ->where(function ($q) use ($filters) {
                                $q->where(function ($q) use ($filters) {
                                    $q->whereBetween('bookings.start_datetime', [$filters['startDateTime'], $filters['endDateTime']])
                                        ->orWhereBetween('bookings.end_datetime', [$filters['startDateTime'], $filters['endDateTime']])
                                        ->orWhere(function ($q) use ($filters) {
                                            $q->where('bookings.start_datetime', '<=', $filters['startDateTime'])
                                                ->where('bookings.end_datetime', '>=', $filters['endDateTime']);
                                        });
                                })
                                    ->whereNotIn('bookings.booking_status', [6, 3]);
                            });

                        if (!empty($bookingId)) {
                            $q->where('bookings.id', '!=', $bookingId);
                        }
                    });
                });

        return $query;
    }

    private function formatVehicle($vehicle)
    {
        $vehicleImagePath = $vehicle->image ?? '';
        $filename = basename($vehicleImagePath);
        $newpath = self::VEHICLE_IMAGE_PATH . $filename;
        $file = public_path(self::STORAGE_PATH . $newpath);

        if (file_exists($file)) {
            $vehicleImagePath = $newpath;
        }

        $vehicle->image = uploadedAsset($vehicleImagePath);
        $vehicle->vehicle_price = number_format((float) $vehicle->vehicle_price, 2, '.', '');
        $vehicle->encrypted_id = customEncrypt($vehicle->id, Booking::$reservationSecretKey);

        return $vehicle;
    }

    public function store(Request $request): array
    {
        $bookingId = $request->booking_id ?? null;
        $successMsg = empty($bookingId) ? __('admin.bookings.reservation_create_success') : __('admin.bookings.reservation_update_success');
        $errorMsg = empty($bookingId) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            DB::beginTransaction();

            $startDate = is_string($request->start_date) ? $request->start_date : '';
            $startTime = is_string($request->start_time) ? $request->start_time : '';
            $endDate = is_string($request->end_date) ? $request->end_date : '';
            $endTime = is_string($request->end_time) ? $request->end_time : '';

            $startDateTime = $startDate . ' ' . $startTime;
            $endDateTime = $endDate   . ' ' . $endTime;
            $startDateTimeCarbon = Carbon::createFromFormat(self::DISPLAY_DATE_FORMAT, $startDateTime);
            $endDateTimeCarbon = Carbon::createFromFormat(self::DISPLAY_DATE_FORMAT, $endDateTime);

            $startDateTime = $startDateTimeCarbon ? $startDateTimeCarbon->format(self::DB_DATE_FORMAT) : null;
            $endDateTime = $endDateTimeCarbon ? $endDateTimeCarbon->format(self::DB_DATE_FORMAT) : null;
            $bookingId = $request->booking_id ?? null;

            $data = [
                'vehicle_id'                => $request->vehicle_id,
                'customer_id'               => $request->customer_id,
                "booking_by"                => "admin",
                'driver_id'                 => $request->driver_id ?? null,
                'driver_price'              => $request->driver_price ?? 0,
                'vehicle_price'             => $request->vehicle_price,
                'total_insurance_price'     => $request->total_insurance_price ?? 0,
                'total_extra_service_price' => $request->total_extra_service_price ?? 0,
                'final_price'               => $request->final_price ?? 0,
                'extra_service'             => $request->extra_service ?? null,
                'insurance'                 => $request->insurance ?? null,
                'security_deposit'          => $request->security_deposit ?? null,
                'start_datetime'            => $startDateTime,
                'end_datetime'              => $endDateTime,
                'pickup_location'           => $request->pickup_location,
                'return_location'           => $request->return_location,
                'booking_status'            => Booking::$booked,
                'booking_tariff'            => $request->tariff ?? null,
                'driving_type'              => $request->driving_type ?? null,
                'rental_type'               => $request->vehicle_price_type ?? null,
                'no_of_passengers'          => $request->no_of_passengers ?? null,
                'no_of_days'                => $request->no_of_days ?? null,
                'vehicle_total_price'       => $request->vehicle_total_price ?? null,
                'booking_date'              => now(),
                'payment_type'              => 'cod'
            ];
            $details = [
                'vehicle_price_type' => $request->vehicle_price_type,
                'vehicle_season_id'  => $request->vehicle_season_id ?? null,
                'vehicle_tariff_id'  => $request->vehicle_tariff_id ?? null,
            ];
            $vehicleTariff = '';
            $vehicleSeason = '';

            if ($request->vehicle_tariff_id) {
                /** @var VehicleTarrif|null $vehicleTariff */
                $vehicleTariff = VehicleTarrif::find($request->vehicle_tariff_id);
                if ($vehicleTariff) {
                    $details['tariff_title'] = $vehicleTariff->tariff_title;
                    $details['tariff_price'] = $vehicleTariff->tariff_daily_price;
                    $details['tariff_from_days'] = $vehicleTariff->tariff_from_days;
                    $details['tariff_to_days'] = $vehicleTariff->tariff_to_days;
                    $details['tariff_base_km'] = $vehicleTariff->tariff_base_km;
                    $details['tariff_extra_price'] = $vehicleTariff->tariff_extra_price;
                }
            }
            if ($request->vehicle_season_id) {
                /** @var VehicleSeason|null $vehicleSeason */
                $vehicleSeason = VehicleSeason::find($request->vehicle_season_id);
                if ($vehicleSeason) {
                    $details['seasonal_title'] = $vehicleSeason->seasonal_title;
                    $details['seasonal_start_date'] = $vehicleSeason->seasonal_start_date;
                    $details['seasonal_end_date'] = $vehicleSeason->seasonal_end_date;
                    $details['seasonal_daily_rate'] = $vehicleSeason->seasonal_daily_rate;
                    $details['seasonal_weekly_rate'] = $vehicleSeason->seasonal_weekly_rate;
                    $details['seasonal_monthly_rate'] = $vehicleSeason->seasonal_monthly_rate;
                    $details['seasonal_late_fee'] = $vehicleSeason->seasonal_late_fee;
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
                    'message'    => 'Reservation created'
                ]);

                $customer = User::where('id', $booking->customer_id)->first();
                $vehicle = VehicleInfo::where('id', $booking->vehicle_id)->first();
                $driver = Driver::find($booking->driver_id);
                $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? self::DEFAULT_COMPANY_NAME;
                $notifyData = [
                    'user_name'       => $customer->name ?? '',
                    'company_name'    => $companyName,
                    'email'           => $customer->email ?? '',
                    'phonenumber'     => $customer->phone_number ?? '',
                    'vehicle_name'    => $vehicle->name ?? "",
                    'driver_name'     => $driver ? $driver->driver_name : "",
                    'reservation_id'  => $booking->reservation_id ?? "",
                    'start_date'      => $booking->start_datetime ? formatDateTime($booking->start_datetime) : "",
                    'end_date'        => $booking->end_datetime ? formatDateTime($booking->end_datetime) : "",
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
                        if ($appAdmin && $appAdmin->email) {
                            sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);
                        }
                    }
                    if (userNotificationsEnabled() && $customer && $customer->email) {
                        sendNotification($customer->email, 'booking-confirmation-to-user', $notifyData);
                    }
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            } else {
                $data['updated_by'] = Auth::guard('admin')->id();

                Booking::where('id', $bookingId)->update($data);
                BookingDetail::where('booking_id', $bookingId)->update($details);

                $booking = Booking::find($bookingId);
                $bookingDetail = BookingDetail::where('booking_id', $bookingId)->first();

                $historyData = [
                    'bookings'        => $booking?->toArray() ?? [],
                    'booking_details' => $bookingDetail?->toArray() ?? [],
                ];
                BookingHistory::create([
                    'booking_id' => $booking instanceof Booking ? $booking->id : null,
                    'action'     => 'update',
                    'data'       => json_encode($historyData),
                    'message'    => 'Reservation updated'
                ]);
            }

            DB::commit();

            $encryptedId = (is_int($bookingId) || is_string($bookingId)) ? customEncrypt($bookingId, Booking::$reservationSecretKey) : null;

            return [
                'code'             => 200,
                'message'          => $successMsg,
                'view_details_url' => route('reservation.details', ['id' => $encryptedId]),
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
        $locations   = Location::where('status', 1)->get();
        $priceTypes  = PricingType::where('type', 1)->get();
        $drivingTypes = DB::table('driving_types')->get();

        $customers = User::select(
            'users.id',
            self::USERNAME_SELECT,
            DB::raw(self::FULLNAME_SELECT),
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
            'locations'   => $locations,
            'priceTypes'  => $priceTypes,
            'drivingTypes'=> $drivingTypes,
            'customers'   => $customers,
            'bookingId'   => $bookingId,
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
                'message' => __('admin.bookings.reservation_delete_success')
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
                'message' => __('admin.bookings.reservation_complete_success')
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
            $query = Booking::select(
                'bookings.id',
                'bookings.reservation_id',
                self::VEHICLE_NAME_SELECT,
                'vehicle_info.vehicle_image',
                DB::raw(self::CUSTOMER_FULLNAME_SELECT),
                self::CUSTOMER_IMAGE_SELECT,
                'users.id as customer_id',
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
                ->join(self::PICKUP_LOCATION_SELECT, 'pickup_location.id', '=', 'bookings.pickup_location')
                ->join(self::DROP_LOCATION_SELECT, 'drop_location.id', '=', 'bookings.return_location')
                ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
                ->where('bookings.booking_by', '!=', 'quotation');

            //  DataTables Search
            if ($request->has('search') && !empty($request->search)) {
                $search = (string) $request->search;
                $query->where(function ($q) use ($search) {
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
                $dates = explode(' - ', $request->sort_by_date);
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
                switch (strtolower($request->sort_by)) {
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
                    default:
                        // fallback: default ordering by latest
                        $query->orderBy('bookings.created_at', 'desc');
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
                ->where('bookings.booking_by', '!=', 'quotation')
                ->count();
            $filteredRecords = $query->count();

            $query->offset($request->start)->limit($request->length);
            $bookings = $query->get();

            // Format Response Data
            $bookings->map(function ($booking) {
                $imagePath = $booking->vehicle_image;
                $filename = basename($imagePath);
                $newpath = self::VEHICLE_IMAGE_PATH . $filename;
                $file = public_path(self::STORAGE_PATH . $newpath);
                if (file_exists($file)) {
                    $imagePath = $newpath;
                }
                $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
                $booking->vehicle_image = uploadedAsset($imagePath);
                $booking->booking_status_text = is_numeric($booking->booking_status)
                    ? Booking::getStatusLabel((int) $booking->booking_status)
                    : null;
                $booking->customer_full_name = ucwords($booking->customer_full_name);

                return $booking;
            });

            return [
                "draw"            => intval($request->draw),
                "recordsTotal"    => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data"            => $bookings
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
                    'message' => 'Booking id is required.'
                ];
            }

            $booking = Booking::select(
                'bookings.*',
                self::VEHICLE_NAME_SELECT,
                'vehicle_info.vehicle_image',
                DB::raw(self::CUSTOMER_FULLNAME_SELECT),
                self::CUSTOMER_IMAGE_SELECT,
                'users.name as user_name',
                'pickup_location.name as pickup_location_name',
                'drop_location.name as drop_location_name',
            )
                ->join('users', 'users.id', '=', 'bookings.customer_id')
                ->join('user_details', 'user_details.user_id', '=', 'users.id')
                ->join(self::PICKUP_LOCATION_SELECT, 'pickup_location.id', '=', 'bookings.pickup_location')
                ->join(self::DROP_LOCATION_SELECT, 'drop_location.id', '=', 'bookings.return_location')
                ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
                ->where('bookings.id', $id)
                ->first();

            if (!empty($booking)) {
                $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
                $vehicleImagePath = $booking->vehicle_image ?? '';
                $filename = basename($vehicleImagePath);
                $newpath = self::VEHICLE_IMAGE_PATH . $filename;
                $file = public_path(self::STORAGE_PATH . $newpath);
                if (file_exists($file)) {
                    $vehicleImagePath = $newpath;
                }
                $booking->vehicle_image = uploadedAsset($vehicleImagePath);

                if ($booking->insurance) {
                    $booking->insurance_formatted = json_decode($booking->insurance);
                }

                if ($booking->extra_service) {
                    $booking->extra_service_formatted = json_decode($booking->extra_service);
                }
                $booking->booking_status_text = Booking::getStatusLabel((int) $booking->booking_status);
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
            'bookings.tax_val',
            self::VEHICLE_NAME_SELECT,
            'vehicle_info.vehicle_image',
            'cartypes.name as vehicle_type',
            'pickup_location.name as pickup_location_name',
            'drop_location.name as drop_location_name',
            DB::raw(self::CUSTOMER_FULLNAME_SELECT),
            self::CUSTOMER_IMAGE_SELECT,
            'users.name as customer_user_name',
            'users.phone_number as customer_phone_number',
            'drivers.driver_name',
            'drivers.image as driver_image',
            'drivers.phone_number as driver_phone_number',
            'booking_details.vehicle_price_type',
            'bookings.rental_type',
            'bookings.delivery_type',
            'bookings.booking_by',
            'driving_types.name as driving_type_name'
        )
            ->leftjoin('booking_details', 'booking_details.booking_id', '=', 'bookings.id')
            ->join('users', 'users.id', '=', 'bookings.customer_id')
            ->leftJoin('user_details', 'user_details.user_id', '=', 'users.id')
            ->join(self::PICKUP_LOCATION_SELECT, 'pickup_location.id', '=', 'bookings.pickup_location')
            ->join(self::DROP_LOCATION_SELECT, 'drop_location.id', '=', 'bookings.return_location')
            ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
            ->leftJoin('cartypes', 'cartypes.id', '=', 'vehicle_info.type_id')
            ->leftjoin('drivers', 'drivers.id', '=', 'bookings.driver_id')
            ->leftjoin('driving_types', 'driving_types.id', '=', 'bookings.driving_type')
            ->where('bookings.id', $bookingId)
            ->firstOrFail();

        if ($booking) {
            $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
            $booking->driver_image = uploadedAsset($booking->driver_image, 'profile');

            $vehicleImagePath = $booking->vehicle_image ?? '';
            $filename = basename($vehicleImagePath);
            $newpath = 'vehicles/images/small/' . $filename;
            $file = public_path(self::STORAGE_PATH . $newpath);

            if (file_exists($file)) {
                $vehicleImagePath = $newpath;
            }
            $booking->vehicle_image = uploadedAsset($vehicleImagePath);

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

            $booking->booking_status_text = Booking::getStatusLabel((int) ($booking->booking_status ?? 4));
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

        return [
            'booking'          => $booking,
            'bookingHistories' => $bookingHistories,
        ];
    }

    public function calculateTotalPrice(Request $request): array
    {
        try {
            $data = $request->all();

            $vehiclePrice = (float) ($data['vehicle_price'] ?? 0);
            $vehiclePriceType = strtolower($data['vehicle_price_type'] ?? 'daily');
            $driverPriceVal = (float) ($data['driver_price'] ?? 0);
            $securityDeposit = (float) ($data['security_deposit'] ?? 0);

            $startDate = Carbon::parse($data['start_date'] . ' ' . $data['start_time']);
            $endDate = Carbon::parse($data['end_date'] . ' ' . $data['end_time']);

            $noOfDays = $startDate->diffInDays($endDate) + 1;
            $noOfMonths = $startDate->diffInMonths($endDate) + 1;
            $noOfYears = $startDate->diffInYears($endDate) + 1;

            switch ($vehiclePriceType) {
                case 'daily':
                    $vehiclePriceRate = $noOfDays * $vehiclePrice;
                    break;

                case 'weekly':
                    $noOfWeeks = ceil($noOfDays / 7);
                    $vehiclePriceRate = $noOfWeeks * $vehiclePrice;
                    break;

                case 'monthly':
                    $vehiclePriceRate = $noOfMonths * $vehiclePrice;
                    break;

                case 'yearly':
                    $vehiclePriceRate = $noOfYears * $vehiclePrice;
                    break;

                default:
                    $vehiclePriceRate = $noOfDays * $vehiclePrice;
                    $vehiclePriceType = 'daily';
                    break;
            }

            $totalExtraServicePrice = 0;
            $extraServiceName = [];
            if (!empty($data['extra_service'])) {
                $extraServices = json_decode($data['extra_service'], true);
                foreach ($extraServices as $service) {
                    if ($service['type'] === 'per_day') {
                        $totalExtraServicePrice += ($noOfDays * (float)$service['price']);
                    } elseif ($service['type'] === 'one_time') {
                        $totalExtraServicePrice += (float)$service['price'];
                    } elseif ($service['type'] === 'percentage') {
                        $totalExtraServicePrice += ($vehiclePriceRate * (float)$service['price']) / 100;
                    }
                    $extraServiceName[] = $service['id'];
                }
            }

            $totalInsurancePrice = 0;
            $insuranceName = [];
            if (!empty($data['insurance'])) {
                $insuranceServices = json_decode($data['insurance'], true);
                foreach ($insuranceServices as $insurance) {
                    if ($insurance['type'] === 'daily') {
                        $totalInsurancePrice += ($noOfDays * (float)$insurance['price']);
                    } elseif ($insurance['type'] === 'fixed') {
                        $totalInsurancePrice += (float)$insurance['price'];
                    } elseif ($insurance['type'] === 'percentage') {
                        $totalInsurancePrice += ($vehiclePriceRate * (float)$insurance['price']) / 100;
                    }
                    $insuranceName[] = $insurance['id'];
                }
            }

            $vehiclePriceRate = number_format($vehiclePriceRate, 2, '.', '');
            $driverPriceVal = number_format($driverPriceVal, 2, '.', '');
            $securityDeposit = number_format($securityDeposit, 2, '.', '');
            $totalExtraServicePrice = number_format($totalExtraServicePrice, 2, '.', '');
            $totalInsurancePrice = number_format($totalInsurancePrice, 2, '.', '');

            $totalPriceVal = number_format(
                (float)$driverPriceVal +
                    (float)$securityDeposit +
                    (float)$vehiclePriceRate +
                    (float)$totalExtraServicePrice,
                2,
                '.',
                ''
            );

            $totalPriceVal2 = number_format(
                (float)$driverPriceVal +
                    (float)$securityDeposit +
                    (float)$vehiclePriceRate +
                    (float)$totalExtraServicePrice +
                    (float)$totalInsurancePrice,
                2,
                '.',
                ''
            );

            $response = [
                'no_of_days'                => $noOfDays,
                'vehicle_price_rate'        => $vehiclePriceRate,
                'vehicle_price_type'        => $vehiclePriceType,
                'driver_price'              => $driverPriceVal,
                'security_deposit'          => $securityDeposit,
                'total_extra_service_price' => $totalExtraServicePrice,
                'total_extra_service'       => count($extraServiceName),
                'total_price_val'           => $totalPriceVal,
                'total_price'               => $totalPriceVal2,
                'total_insurance_price'     => $totalInsurancePrice,
                'total_insurance'           => count($insuranceName),
                'insurance_name'            => implode(', ', $insuranceName),
                'extra_service_name'        => implode(', ', $extraServiceName),
            ];

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
            $bookingId = $request->booking_id;
            $booking = Booking::find($bookingId);

            if (!$booking) {
                return [
                    'code'    => 404,
                    'message' => __('admin.bookings.not_found'),
                ];
            }

            if ($booking instanceof Booking) {
                $booking->update([
                    'cancel_reason'  => $request->cancel_reason,
                    'cancel_by'      => Auth::guard('admin')->id() ?? $request->user_id,
                    'cancel_date'    => now(),
                    'booking_status' => 6,
                ]);
            }

            $bookingDetail = BookingDetail::where('booking_id', $bookingId)->first();

            $historyData = [
                'bookings'         => $booking->toArray(),
                'booking_details'  => $bookingDetail ? $bookingDetail->toArray() : [],
            ];
            if ($booking instanceof \Illuminate\Database\Eloquent\Collection) {
                foreach ($booking as $singleBooking) {
                    BookingHistory::create([
                        'booking_id' => $singleBooking->id,
                        'action'     => 'cancel',
                        'data'       => json_encode($historyData),
                        'message'    => 'Booking cancelled',
                    ]);

                    $customer = User::find($singleBooking->customer_id);
                    $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? self::DEFAULT_COMPANY_NAME;
                    $vehicle = VehicleInfo::where('id', $singleBooking->vehicle_id)->first();
                    $driver = Driver::find($singleBooking->driver_id);

                    $notifyData = [
                        'user_name'       => $customer->name ?? '',
                        'company_name'    => $companyName,
                        'email'           => $customer->email ?? '',
                        'phonenumber'     => $customer->phone_number ?? '',
                        'vehicle_name'    => $vehicle->name ?? "",
                        'driver_name'     => $driver ? $driver->driver_name : "",
                        'reservation_id'  => $singleBooking->reservation_id ?? "",
                        'start_date'      => $singleBooking->start_datetime ? formatDateTime($singleBooking->start_datetime) : "",
                        'end_date'        => $singleBooking->end_datetime ? formatDateTime($singleBooking->end_datetime) : "",
                        'pickup_location' => $singleBooking->pickupLocation ? $singleBooking->pickupLocation->name : "",
                        'delivery_type'   => $singleBooking->delivery_type ?? "",
                        'rental_type'     => $singleBooking->rental_type ?? "",
                        'payment_type'    => $singleBooking->payment_type ?? "",
                        'payment_status'  => $singleBooking->payment_status ?? "",
                        'tototal_amount'  => $singleBooking->final_price ?? ""
                    ];
                }
            } else {
                BookingHistory::create([
                    'booking_id' => $booking->id,
                    'action'     => 'cancel',
                    'data'       => json_encode($historyData),
                    'message'    => 'Booking cancelled',
                ]);

                $customer = User::find($booking->customer_id);
                $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? self::DEFAULT_COMPANY_NAME;
                $vehicle = VehicleInfo::where('id', $booking->vehicle_id)->first();
                $driver = Driver::find($booking->driver_id);

                $notifyData = [
                    'user_name'       => $customer->name ?? '',
                    'company_name'    => $companyName,
                    'email'           => $customer->email ?? '',
                    'phonenumber'     => $customer->phone_number ?? '',
                    'vehicle_name'    => $vehicle->name ?? "",
                    'driver_name'     => $driver ? $driver->driver_name : "",
                    'reservation_id'  => $booking->reservation_id ?? "",
                    'start_date'      => $booking->start_datetime ? formatDateTime($booking->start_datetime) : "",
                    'end_date'        => $booking->end_datetime ? formatDateTime($booking->end_datetime) : "",
                    'pickup_location' => $booking->pickupLocation ? $booking->pickupLocation->name : "",
                    'delivery_type'   => $booking->delivery_type ?? "",
                    'rental_type'     => $booking->rental_type ?? "",
                    'payment_type'    => $booking->payment_type ?? "",
                    'payment_status'  => $booking->payment_status ?? "",
                    'tototal_amount'  => $booking->final_price ?? ""
                ];
            }

            try {
                $appAdmin = User::where('user_type', 1)->first();
                if ($appAdmin && isset($appAdmin->email)) {
                    sendNotification($appAdmin->email, 'booking-cancelled-to-admin', $notifyData ?? []);
                }

                if (isset($customer) && isset($notifyData) && !empty($customer->email)) {
                    sendNotification($customer->email, 'booking-cancelled-to-user', $notifyData);
                }
                if (isset($customer) && isset($notifyData) && !empty($customer->email)) {
                    sendNotification($customer->email, 'booking-cancelled-to-user', $notifyData);
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }

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
