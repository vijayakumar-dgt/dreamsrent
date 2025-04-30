<?php

namespace Modules\CarInfo\Models;

use App\Models\Review;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\CarFactory;
class VehicleInfo extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'language_id',
        'parent_id',
        'name',
        'vehicle_image',
        'slug',
        'perma_link',
        'category_id',
        'type_id',
        'brand_id',
        'model_id',
        'plate_number',
        'vin',
        'main_location_id',
        'other_location_id',
        'fuel_type_id',
        'odometer',
        'color_id',
        'year',
        'transmission_id',
        'mileage',
        'passenger_capacity',
        'num_seats',
        'num_doors',
        'num_airbags',
        'vehicle_price',
        'vehicle_video',
        'vehicle_basekm',
        'vehicle_extrakmprice',
        'vehicle_metatitle',
        'vehicle_metadesc',
        'vehicle_metakeywords',
        'features',
        'views',
        'description',
        'created_by',
        'created_at',
    ];

    protected $appends = ['vehicle_image_url'];
    protected $table = "vehicle_info";
    // protected static function newFactory(): CarFactory
    // {
    //     // return CarFactory::new();
    // }

    public function carType()
    {
        return $this->belongsTo(Cartype::class, 'type_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'vehicle_id');
    }


    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function mainLocation()
    {
        return $this->belongsTo(Location::class, 'main_location_id');
    }

    public function color()
    {
        return $this->belongsTo(CarColor::class, 'color_id');
    }

    public function fuel_type()
    {
        return $this->belongsTo(CarFuel::class, 'fuel_type_id');
    }

    public function transmission()
    {
        return $this->belongsTo(Transmission::class, 'transmission_id');
    }

    public function getVehicleImageUrlAttribute()
    {
        return $this->vehicle_image ? asset('storage/' . $this->vehicle_image) : null;
    }

    public function faqs()
    {
        return $this->hasMany(VehicleFaq::class, 'vehicle_id', 'id');
    }

    public function damages()
    {
        return $this->hasMany(VehicleDamage::class, 'vehicle_id', 'id');
    }

    public function tariffs()
    {
        return $this->hasMany(VehicleTarrif::class, 'vehicle_id', 'id');
    }

    public function seasonals()
    {
        return $this->hasMany(VehicleSeason::class, 'vehicle_id', 'id');
    }

    public function extraservices()
    {
        return $this->hasMany(VehicleExtraService::class, 'vehicle_id', 'id');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'vehicle_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
