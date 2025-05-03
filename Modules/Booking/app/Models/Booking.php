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

/**
 * @property string|null $booking_date
 * @property string|null $vehicle_image
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
 * 
 */

class Booking extends Model
{
    use SoftDeletes;

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

    // Booking Status Constants
    public static $inprogress = 1;
    public static $confirmed = 2;
    public static $rejected = 3;
    public static $booked = 4;
    public static $completed = 5;
    public static $cancelled = 6;

    public static $reservationSecretKey = 'ReservationId';

    protected $appends = ['encrypted_id'];

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
        return customEncrypt($this->id, Booking::$reservationSecretKey);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(VehicleInfo::class, 'vehicle_id');
    }

    public function drivingType(): BelongsTo
    {
        return $this->belongsTo(DrivingType::class, 'driving_type');
    }

    public function bookingDetail(): HasOne
    {
        return $this->hasOne(BookingDetail::class, 'booking_id');
    }

    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'pickup_location');
    }

    public function returnLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'return_location');
    }

    public function cancelledUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancel_by');
    }
    /**
     * @return HasOne<BookingUserInfo, Booking>
     */
    public function userInfo(): HasOne
    {
         /** @var hasOne<BookingUserInfo, Booking> */
        return $this->hasOne(BookingUserInfo::class, 'booking_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function customerDetail(): BelongsTo
    {
        return $this->belongsTo(UserDetail::class, 'customer_id', 'user_id');
    }
}
