<?php

namespace Modules\Booking\Models;

use App\Models\DrivingType;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\VehicleInfo;

// use Modules\Booking\Database\Factories\BookingFactory;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

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

    public function getEncryptedIdAttribute()
    {
        return customEncrypt($this->id, Booking::$reservationSecretKey);
    }

    public function vehicle()
    {
        return $this->belongsTo(VehicleInfo::class, 'vehicle_id');
    }

    public function drivingType()
    {
        return $this->belongsTo(DrivingType::class, 'driving_type');
    }

    public function bookingDetail()
    {
        return $this->hasOne(BookingDetail::class, 'booking_id');
    }

    public function pickupLocation()
    {
        return $this->belongsTo(Location::class, 'pickup_location');
    }

    public function returnLocation()
    {
        return $this->belongsTo(Location::class, 'return_location');
    }

    public function cancelledUser()
    {
        return $this->belongsTo(User::class, 'cancel_by');
    }

    public function userInfo()
    {
        return $this->hasOne(BookingUserInfo::class, 'booking_id');
    }

    public function customer(){
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function customerDetail(){
        return $this->belongsTo(UserDetail::class, 'customer_id', 'user_id');
    }
}
