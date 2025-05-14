<?php

namespace Modules\Booking\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
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
use Modules\GeneralSetting\Models\InsuranceBenefit;

class BookingController extends Controller
{
    public function index(): View
    {
        return view('booking::reservation.index');
    }

    public function create(): View
    {
        $locations = Location::where('status', 1)->get();
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

        return view('booking::reservation.add', compact('locations', 'priceTypes', 'drivingTypes', 'customers'));
    }

    public function getCustomerDetails(Request $request): JsonResponse
    {
        try {
            $customerId = $request->customer_id ?? '';
            $customer = User::select(
                'users.id',
                'users.name as username',
                DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as full_name"),
                DB::raw("(SELECT COUNT(*) FROM bookings WHERE bookings.customer_id = users.id) as bookings_count"),
                'users.email',
                'users.phone_number',
                'user_details.profile_image'
            )
                ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                ->where(['users.user_type' => 3, 'users.status' => 1, 'users.id' => $customerId])
                ->first();

            if ($customer) {
                $customer->full_name = $customer->full_name ?? $customer->username;
                $customer->profile_image = uploadedAsset(is_string($customer->profile_image) ? $customer->profile_image : null, 'profile');
            }

            return response()->json([
                'code'   => 200,
                'message' => 'Customer retrieved successfully.',
                'data' => $customer,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFilterVehicles(Request $request): JsonResponse
    {
        try {
            $orderBy = $request->order_by ?? 'desc';
            $search = $request->search ?? null;
            $perPage = $request->per_page ?? 1;
            $page = $request->page ?? 10;

            $startDateTime = '';
            $endDateTime = '';
            $startDateFormat = '';
            $endDateFormat = '';
            $startDate = $request->start_date ?? null;
            $endDate = $request->end_date ?? null;
            $startTime = $request->start_time ?? null;
            $endTime = $request->end_time ?? null;
            $tariff = $request->tariff ?? null;
            $brandIds = $request->brand_ids ?? null;
            $modelIds = $request->model_ids ?? null;
            $typeIds = $request->type_ids ?? null;
            $colorIds = $request->color_ids ?? null;
            $pickupLocation = $request->pickup_location ?? null;
            $returnLocation = $request->return_location ?? null;
            $bookingId = $request->booking_id ?? null;

            if (!empty($startDate) && is_string($startDate)) {
                $dateTimeString = $startDate;
            
                if (!empty($startTime) && is_string($startTime)) {
                    $dateTimeString .= ' ' . $startTime;
                    $startDateCarbon = Carbon::createFromFormat('d-m-Y H:i', $dateTimeString);
                } else {
                    $startDateCarbon = Carbon::createFromFormat('d-m-Y', $dateTimeString);
                }
            
                if ($startDateCarbon instanceof Carbon) {
                    $startDateTime = $startDateCarbon->format('Y-m-d H:i:s');
                }

                $startDateCarbonOnly = Carbon::createFromFormat('d-m-Y', $startDate);
                if ($startDateCarbonOnly instanceof Carbon) {
                    $startDateFormat = $startDateCarbonOnly->format('Y-m-d');
                }
            }

            if (!empty($endDate) && is_string($endDate)) {
                $dateTimeString = $endDate;
            
                if (!empty($endTime) && is_string($endTime)) {
                    $dateTimeString .= ' ' . $endTime;
                    $endDateCarbon = Carbon::createFromFormat('d-m-Y H:i', $dateTimeString);
                } else {
                    $endDateCarbon = Carbon::createFromFormat('d-m-Y', $dateTimeString);
                }
            
                if ($endDateCarbon instanceof Carbon) {
                    $endDateTime = $endDateCarbon->format('Y-m-d H:i:s');
                }

                $endDateCarbonOnly = Carbon::createFromFormat('d-m-Y', $endDate);
                if ($endDateCarbonOnly instanceof Carbon) {
                    $endDateFormat = $endDateCarbonOnly->format('Y-m-d');
                }
            }
            
            $vehicles = VehicleInfo::select(
                'vehicle_info.id',
                'vehicle_info.vehicle_image as image',
                'vehicle_info.name as vehicle_name',
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

                ->whereDoesntHave('maintenances', function ($query) use ($startDateFormat, $endDateFormat) {
                    $query->where(function ($q) use ($startDateFormat, $endDateFormat) {
                        $q->where('maintenances.start_date', '<=', $endDateFormat)
                            ->where('maintenances.end_date', '>=', $startDateFormat)
                            ->where('maintenances.status', '!=', 3);
                    });
                })

                ->when(!empty($brandIds), fn($query) => $query->whereIn('vehicle_info.brand_id', $brandIds))
                ->when(!empty($typeIds), fn($query) => $query->whereIn('vehicle_info.type_id', $typeIds))
                ->when(!empty($modelIds), fn($query) => $query->whereIn('vehicle_info.model_id', $modelIds))
                ->when(!empty($colorIds), fn($query) => $query->whereIn('vehicle_info.color_id', $colorIds))

                ->when(!empty($pickupLocation), function ($query) use ($pickupLocation) {
                    return $query->where(function ($q) use ($pickupLocation) {
                        $q->where('vehicle_info.main_location_id', '=', $pickupLocation)
                            ->orWhereJsonContains('vehicle_info.other_location_id', (string) $pickupLocation);
                    });
                })
                ->when(!empty($returnLocation), function ($query) use ($returnLocation) {
                    return $query->where(function ($q) use ($returnLocation) {
                        $q->where('vehicle_info.main_location_id', '=', $returnLocation)
                            ->orWhereJsonContains('vehicle_info.other_location_id', (string) $returnLocation);
                    });
                })

                ->when($search, function ($query) use ($search, $tariff) {
                    $search = (string) $search;
                    $tariff = (string) $tariff;
                    return $query->where(function ($q) use ($search, $tariff) {
                        $q->where('vehicle_info.year', 'LIKE', "%{$search}%")
                            ->orWhere('vehicle_info.name', 'LIKE', "%{$search}%")
                            ->orWhere('brands.brand_name', 'LIKE', "%{$search}%")
                            ->orWhere('car_models.model_name', 'LIKE', "%{$search}%")
                            ->orWhere('cartypes.name', 'LIKE', "%{$search}%")
                            ->orWhere('car_colors.name', 'LIKE', "%{$search}%")
                            ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(vehicle_info.vehicle_price, '$[0].$tariff')) LIKE ?", ["%{$search}%"]);
                    });
                })

                ->when($tariff, function ($query) use ($tariff, $startDateTime, $endDateTime) {
                    $noOfDays = 1;

                    if (!empty($startDateTime) && !empty($endDateTime)) {
                        $start = Carbon::parse($startDateTime);
                        $end = Carbon::parse($endDateTime);
                        $diffMinutes = $start->diffInMinutes($end);
                        $noOfDays = ceil($diffMinutes / 1440);
                    }

                    return $query->join('vehicle_tarrifs', function ($join) {
                        $join->on('vehicle_tarrifs.vehicle_id', '=', 'vehicle_info.id');
                    })
                        ->where(function ($q) use ($noOfDays) {
                            $q->whereRaw('CAST(vehicle_tarrifs.tariff_from_days AS UNSIGNED) <= ?', [$noOfDays])
                                ->whereRaw('CAST(vehicle_tarrifs.tariff_to_days AS UNSIGNED) = ?', [$noOfDays]);
                        })
                        ->selectRaw("
                        vehicle_tarrifs.id as vehicle_tariff_id,
                        vehicle_tarrifs.tariff_daily_price as vehicle_price,
                        ? as vehicle_price_type
                    ", [$tariff]);
                })

                ->when(empty($tariff), function ($query) use ($startDateTime, $endDateTime) {
                    $noOfDays = 1;

                    if (!empty($startDateTime) && !empty($endDateTime)) {
                        $start = Carbon::parse($startDateTime);
                        $end = Carbon::parse($endDateTime);
                        $diffMinutes = $start->diffInMinutes($end);
                        $noOfDays = ceil($diffMinutes / 1440);
                    }
                    $start = $startDateTime ? Carbon::parse($startDateTime) : Carbon::now();
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

                    $end = $endDateTime ? Carbon::parse($endDateTime) : Carbon::now();
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
                ->when(!empty($startDateTime) || !empty($endDateTime), function ($query) use ($startDateTime, $endDateTime, $bookingId) {
                    $query->whereNotExists(function ($q) use ($startDateTime, $endDateTime, $bookingId) {
                        $q->select(DB::raw(1))
                            ->from('bookings')
                            ->whereRaw('bookings.vehicle_id = vehicle_info.id')
                            ->where(function ($q) use ($startDateTime, $endDateTime) {
                                $q->where(function ($q) use ($startDateTime, $endDateTime) {
                                    $q->whereBetween('bookings.start_datetime', [$startDateTime, $endDateTime])
                                        ->orWhereBetween('bookings.end_datetime', [$startDateTime, $endDateTime])
                                        ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                                            $q->where('bookings.start_datetime', '<=', $startDateTime)
                                                ->where('bookings.end_datetime', '>=', $endDateTime);
                                        });
                                })
                                    ->whereNotIn('bookings.booking_status', [6, 3]);
                            });

                        if (!empty($bookingId)) {
                            $q->where('bookings.id', '!=', $bookingId);
                        }
                    });
                })

                ->orderBy('vehicle_info.id', $orderBy)
                ->paginate($perPage, ['*'], 'page', $page);

            $vehicles->getCollection()->map(function ($vehicle) {
                $vehicle->image = uploadedAsset($vehicle->image);
                $vehicle->vehicle_price = number_format((float) $vehicle->vehicle_price, 2, '.', '');
                $vehicle->encrypted_id = customEncrypt($vehicle->id, Booking::$reservationSecretKey);
                return $vehicle;
            });

            return response()->json([
                'code' => 200,
                'message' => __('Vehicles retrieved successfully.'),
                'data' => $vehicles,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required',
            'start_time' => 'required',
            'end_date' => 'required',
            'end_time' => 'required',
            'pickup_location' => 'required',
            'return_location' => 'required',
            'vehicle_id' => 'required',
            'customer_id' => 'required',
            'vehicle_price' => 'required',
            'extra_service' => 'required',
            'insurance' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Validation error!'),
                'errors' => $validator->errors(),
            ], 400);
        }

        $bookingId = $request->booking_id ?? null;
        $successMsg = empty($bookingId) ? __('admin.bookings.reservation_create_success') : __('admin.bookings.reservation_update_success');
        $errorMsg = empty($bookingId) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            DB::beginTransaction();

            $startDate = is_string($request->start_date) ? $request->start_date : '';
            $startTime = is_string($request->start_time) ? $request->start_time : '';
            $endDate   = is_string($request->end_date)   ? $request->end_date   : '';
            $endTime   = is_string($request->end_time)   ? $request->end_time   : '';

            $startDateTime = $startDate . ' ' . $startTime;
            $endDateTime   = $endDate   . ' ' . $endTime;
            $startDateTimeCarbon = Carbon::createFromFormat('d-m-Y H:i', $startDateTime);
            $endDateTimeCarbon = Carbon::createFromFormat('d-m-Y H:i', $endDateTime);

            $startDateTime = $startDateTimeCarbon ? $startDateTimeCarbon->format('Y-m-d H:i:s') : null;
            $endDateTime = $endDateTimeCarbon ? $endDateTimeCarbon->format('Y-m-d H:i:s') : null;
            $bookingId = $request->booking_id ?? null;

            $data = [
                'vehicle_id' => $request->vehicle_id,
                'customer_id' => $request->customer_id,
                "booking_by" => "admin",
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
                'booking_status' => Booking::$booked,
                'booking_tariff' => $request->tariff ?? null,
                'driving_type' => $request->driving_type ?? null,
                'rental_type' => $request->vehicle_price_type ?? null,
                'no_of_passengers' => $request->no_of_passengers ?? null,
                'no_of_days' => $request->no_of_days ?? null,
                'vehicle_total_price' => $request->vehicle_total_price ?? null,
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
                if (rentalNotificationEnabled()) {
                    $appAdmin = User::where('user_type', 1)->first();
                    if ($appAdmin && $appAdmin->email) {
                        sendNotification($appAdmin->email, 'booking-confirmation-to-admin', $notifyData);
                    }
                }
                if(userNotificationsEnabled() && $customer && $customer->email){
                        sendNotification($customer->email, 'booking-confirmation-to-user', $notifyData);
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

            return response()->json([
                'code' => 200,
                'message' => $successMsg,
                'view_details_url' => route('reservation.details', ['id' => $encryptedId]),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'code' => 500,
                'message' => $errorMsg,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request, string|int|null $id): View
    {
        $locations = Location::where('status', 1)->get();
        $priceTypes = PricingType::where('type', 1)->get();
        $drivingTypes = DB::table('driving_types')->get();
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
        $bookingId = customDecrypt($id, Booking::$reservationSecretKey);

        return view('booking::reservation.edit', compact('locations', 'priceTypes', 'drivingTypes', 'customers', 'bookingId'));
    }
    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            Booking::where('id', $id)->delete();
            BookingDetail::where('booking_id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.bookings.reservation_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error'),
            ], 500);
        }
    }

    public function complete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            Booking::where('id', $id)->update(['booking_status' => 5]);

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.bookings.reservation_complete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error'),
            ], 500);
        }
    }

    public function bookingList(Request $request): JsonResponse
    {
        try {
            $query = Booking::select(
                'bookings.id',
                'bookings.reservation_id',
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
                $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
                $booking->vehicle_image = uploadedAsset($booking->vehicle_image);
                $booking->booking_status_text = is_numeric($booking->booking_status)
                    ? Booking::getStatusLabel((int) $booking->booking_status)
                    : null;

                return $booking;
            });

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getBookingDetails(Request $request): JsonResponse
    {
        try {
            $id = $request->booking_id ?? '';

            if (empty($id)) {
                return response()->json([
                    'status' => 'error',
                    'code'   => 400,
                    'message' => 'Booking id is required.'
                ], 400);
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
                $booking->vehicle_image = uploadedAsset($booking->vehicle_image);
                if ($booking->insurance) {
                    $booking->insurance_formatted = json_decode($booking->insurance);
                }

                if ($booking->extra_service) {
                    $booking->extra_service_formatted = json_decode($booking->extra_service);
                }
                $booking->booking_status_text = Booking::getStatusLabel((int) $booking->booking_status);
            }

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $booking,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function reservationViewDetails(Request $request, string|int|null $id): View
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
            'bookings.booking_by'
        )
            ->leftjoin('booking_details', 'booking_details.booking_id', '=', 'bookings.id')
            ->join('users', 'users.id', '=', 'bookings.customer_id')
            ->leftJoin('user_details', 'user_details.user_id', '=', 'users.id')
            ->join('locations as pickup_location', 'pickup_location.id', '=', 'bookings.pickup_location')
            ->join('locations as drop_location', 'drop_location.id', '=', 'bookings.return_location')
            ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
            ->leftJoin('cartypes', 'cartypes.id', '=', 'vehicle_info.type_id')
            ->leftjoin('drivers', 'drivers.id', '=', 'bookings.driver_id')
            ->where('bookings.id', $bookingId)
            ->first();

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

            if (!empty($booking->insurance)) {
                $insuranceArray = json_decode($booking->insurance, true);
                if (is_array($insuranceArray)) {
                    $booking->insurance_formatted = $insuranceArray;
                    $booking->insurance_count = count($insuranceArray);
                    $insuranceIds = collect($insuranceArray)->pluck('id')->toArray();
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

        return view('booking::reservation.view_details', compact('booking', 'bookingHistories'));
    }

    public function calculateTotalPrice(Request $request): JsonResponse
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
                'no_of_days' => $noOfDays,
                'vehicle_price_rate' => $vehiclePriceRate,
                'vehicle_price_type' => $vehiclePriceType,
                'driver_price' => $driverPriceVal,
                'security_deposit' => $securityDeposit,
                'total_extra_service_price' => $totalExtraServicePrice,
                'total_extra_service' => count($extraServiceName),
                'total_price_val' => $totalPriceVal,
                'total_price' => $totalPriceVal2,
                'total_insurance_price' => $totalInsurancePrice,
                'total_insurance' => count($insuranceName),
                'insurance_name' => implode(', ', $insuranceName),
                'extra_service_name' => implode(', ', $extraServiceName),
            ];

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $response,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => 'An error occurred while calculating the total price.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancelBooking(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $bookingId = $request->booking_id;
            $booking = Booking::find($bookingId);

            if (!$booking) {
                return response()->json([
                    'code' => 404,
                    'message' => __('admin.bookings.not_found'),
                ], 404);
            }

            if ($booking instanceof Booking) {
                $booking->update([
                    'cancel_reason' => $request->cancel_reason,
                    'cancel_by' => Auth::guard('admin')->id() ?? $request->user_id,
                    'cancel_date' => now(),
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
                    $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
                    $vehicle = VehicleInfo::where('id', $singleBooking->vehicle_id)->first();
                    $driver  = Driver::find($singleBooking->driver_id);

                    $notifyData = [
                        'user_name' => $customer->name ?? '',
                        'company_name' => $companyName,
                        'email'     => $customer->email ?? '',
                        'phonenumber' => $customer->phone_number ?? '',
                        'vehicle_name' => $vehicle->name ?? "",
                        'driver_name'  => $driver ? $driver->driver_name : "",
                        'reservation_id' => $singleBooking->reservation_id ?? "",
                        'start_date'     => $singleBooking->start_datetime ? formatDateTime($singleBooking->start_datetime) : "",
                        'end_date'       => $singleBooking->end_datetime ? formatDateTime($singleBooking->end_datetime) : "",
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
                $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
                $vehicle = VehicleInfo::where('id', $booking->vehicle_id)->first();
                $driver  = Driver::find($booking->driver_id);

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
            }
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


            DB::commit();
            return response()->json([
                'code' => 200,
                'message' => __('admin.bookings.reservation_cancel_success'),
                'redirect_url' => route('reservation.index'),
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => __('admin.bookings.reservation_cancel_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
