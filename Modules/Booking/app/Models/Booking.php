<?php

namespace Modules\Booking\Models;

use App\Models\DrivingType;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\Booking\Models\BookingDetail;
use Modules\Booking\Models\BookingUserInfo;

/**
 * @property string|null $booking_date
 * @property string|null $extra_service_names
 * @property int $insurance_count
 * @property string|null $driver_image
 * @property string|array<string>|null $vehicle_image_url
 * @property string|null $start_datetime
 * @property string|null $end_datetime
 * @property int|null $day_count
 * @property string|null $booking_by
 * @property string|null $payment_status
 * @property string|null $final_price
 * @property string|int|null $booking_status
 * @property string|int|null $customer_id
 * @property string|null $created_at
 * @property int|string|null $driving_type
 * @property string|null $delivery_type
 * @property \App\Models\DrivingType|null $driver_type_info
 * @property string|null $pickup_location
 * @property string|null $return_location
 * @property string|null $delivery_location
 * @property string|null $delivery_return_location
 * @property-read \Modules\CarInfo\Models\VehicleInfo|null $vehicle
 * @property int $reservation_id
 * @property string|null $payment_type
 * @property-read \Modules\Booking\Models\BookingUserInfo|null $userInfo
 * @property int $vehicle_id
 * @property string|null $extra_service
 * @property int|null $driver_id
 * @property string|null $reservation_id
 * @property string|null $start_datetime
 * @property string|null $end_datetime
 * @property string|null $delivery_type
 * @property string|null $rental_type
 * @property string|null $payment_type
 * @property string|null $payment_status
 * @property float|null $total_extra_service_price
 * @property float|null $total_insurance_price
 * @property float|null $vehicle_total_price
 * @property float|null $vehicle_price
 * @property float|null $driver_price
 * @property string|null $currency_symbol
 * @property array<string>|null $insurance_benefits
 * @property float|null $final_price
 * @property float|null $extra_service_count
 * @property float|null $insurance_count
 * @property array<string>|null $extra_service_names
 * @property string|null $transaction_id
 * @property string|null $transaction_id
 * @property-read \Modules\GeneralSetting\Models\Location|null $pickupLocation
 * @property int $id
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Modules\CarInfo\Models\VehicleInfo|null $vehicle
 * @property-read Location|null $pickupLocation
 * @property string|null $customer_image
 * @property string|null $vehicle_image
 * @property string|int|null $booking_status_text
 * @property string|null $insurance
 * @property int|null $extra_service_count
 *
 */

class Booking extends Model
{
    use SoftDeletes;

    public static string $reservationSecretKey = 'ReservationId';

    protected $appends = ['encrypted_id'];

    protected $casts = [
        'insurance' => 'array',
        'extra_service' => 'array',
        'extra_service_names' => 'string',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'vehicle_id',
        'booking_by',
        'reservation_id',
        'booking_status',
        'booking_date',
        'start_datetime',
        'end_datetime',
        'pickup_location',
        'return_location',
        'security_deposit',
        'booking_tariff',
        'driving_type',
        'no_of_passengers',
        'no_of_days',
        'customer_id',
        'driver_id',
        'driver_price',
        'extra_service',
        'insurance',
        'total_insurance_price',
        'total_extra_service_price',
        'vehicle_price',
        'vehicle_total_price',
        'final_price',
        'cancel_date',
        'cancel_by',
        'cancel_reason',
        'created_by',
        'updated_by',
        'rental_type',
        'delivery_location',
        'delivery_return_location',
        'delivery_type',
        'transaction_id',
        'payment_status',
        'payment_type',
        'createed_at',
        'base_km',
        'km_extra_price',
        'expenses',
        'delivery_price',
        'tax_val',
        'tax_type',
    ];

    // Booking Status Constants with type declaration
    public static int $inprogress = 1;
    public static int $confirmed = 2;
    public static int $rejected = 3;
    public static int $booked = 4;
    public static int $completed = 5;
    public static int $cancelled = 6;

    public const RESERVATION_SECRET_KEY = 'ReservationId';

    /**
     * Get the status label for a given status.
     *
     * @param int $status
     * @return string
     */
    public static function getStatusLabel($status)
    {
        $statuses = [
            self::$inprogress => __('admin.common.in_progress'),
            self::$confirmed  => __('admin.common.confirmed'),
            self::$rejected   => __('admin.common.rejected'),
            self::$booked   => __('admin.common.booked'),
            self::$completed  => __('admin.common.completed'),
            self::$cancelled  => __('admin.common.cancelled'),
        ];

        return $statuses[$status] ?? 'Unknown';
    }

    public function getEncryptedIdAttribute(): string
    {
        return customEncrypt($this->id, self::RESERVATION_SECRET_KEY);
    }

    /**
     * @return BelongsTo<\Modules\CarInfo\Models\VehicleInfo, \Modules\Booking\Models\Booking>
     */
    public function vehicle(): BelongsTo
    {
        /** @var BelongsTo<VehicleInfo, Booking> */
        return $this->belongsTo(VehicleInfo::class, 'vehicle_id');
    }

    /**
     * @return BelongsTo<\App\Models\DrivingType, \Modules\Booking\Models\Booking>
     */
    public function drivingType(): BelongsTo
    {
        /** @var BelongsTo<DrivingType, Booking> */
        return $this->belongsTo(DrivingType::class, 'driving_type');
    }

    /**
     * @return HasOne<\Modules\Booking\Models\BookingDetail, \Modules\Booking\Models\Booking>
     */
    public function bookingDetail(): HasOne
    {
        /** @var HasOne<BookingDetail, Booking> */
        return $this->hasOne(BookingDetail::class, 'booking_id');
    }

    /**
     * @return BelongsTo<\Modules\CarInfo\Models\Location, \Modules\Booking\Models\Booking>
     */
    public function pickupLocation(): BelongsTo
    {
        /** @var BelongsTo<Location, Booking> */
        return $this->belongsTo(Location::class, 'pickup_location');
    }

    /**
     * @return BelongsTo<\Modules\CarInfo\Models\Location, \Modules\Booking\Models\Booking>
     */
    public function returnLocation(): BelongsTo
    {
        /** @var BelongsTo<Location, Booking> */
        return $this->belongsTo(Location::class, 'return_location');
    }

    /**
     * @return BelongsTo<\App\Models\User, \Modules\Booking\Models\Booking>
     */
    public function cancelledUser(): BelongsTo
    {
        /** @var BelongsTo<User, Booking> */
        return $this->belongsTo(User::class, 'cancel_by');
    }

    /**
     * @return HasOne<\Modules\Booking\Models\BookingUserInfo, \Modules\Booking\Models\Booking>
     */
    public function userInfo(): HasOne
    {
        /** @var HasOne<BookingUserInfo, Booking> */
        return $this->hasOne(BookingUserInfo::class, 'booking_id');
    }

    /**
     * @return BelongsTo<\App\Models\User, \Modules\Booking\Models\Booking>
     */
    public function customer(): BelongsTo
    {
        /** @var BelongsTo<User, Booking> */
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * @return BelongsTo<\App\Models\UserDetail, \Modules\Booking\Models\Booking>
     */
    public function customerDetail(): BelongsTo
    {
        /** @var BelongsTo<UserDetail, Booking> */
        return $this->belongsTo(UserDetail::class, 'customer_id', 'user_id');
    }
}
