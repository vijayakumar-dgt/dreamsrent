<?php

namespace Modules\CarInfo\Models;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CarInfo\Models\Brand;
use Modules\CarInfo\Models\Cartype;
use Modules\CarInfo\Models\Category;
use Modules\CarInfo\Models\CarColor;
use Modules\CarInfo\Models\CarFuel;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\Maintenance;
use Modules\CarInfo\Models\Transmission;
use Modules\CarInfo\Models\VehicleDamage;
use Modules\CarInfo\Models\VehicleExtraService;
use Modules\CarInfo\Models\VehicleFaq;
use Modules\CarInfo\Models\VehicleSeason;
use Modules\CarInfo\Models\VehicleTarrif;

/**
 * @property int $id
 * @property string $name
 * @property string|null $vehicle_image
 * @property string|null $slug
 * @property int|null $year
 * @property int|null $mileage
 * @property int|null $odometer
 * @property int|null $passenger_capacity
 * @property int|null $num_seats
 * @property int|null $num_doors
 * @property int|null $num_airbags
 * @property string|null $vehicle_video
 * @property string|null $vehicle_metatitle
 * @property string|null $vehicle_metakeywords
 * @property string|null $vehicle_metadesc
 * @property string|null $features
 * @property string|null $vehicle_image_url
 * @property string|null $location_name
 * @property string|null $image_url
 * @property int|null $main_location_id
 * @property int|null $parent_id
 * @property string|null $vehicle_price
 * @property int|null $brand_id
 * @property array<string,mixed> $multiple_vehicle_images
 * @property bool $has_multiple_image
 * @property string|null $avatar_image
 * @property int $damage_count
 * @property int $status
 * @property int $created_at
 * @property string|null $updated_at
 * @property int $created_by
 * @property string|null $vin
 * @property string|null $description
 * @property string|null $vehicle_name
 * @property string|null $cartype_name
 * @property string $name
 * @property string|null $image
 * @property string|null $encrypted_id
 * @property string|null $created_date
 * @property bool $popular
 */
class VehicleInfo extends Model
{
    public ?string $location_name = null;
    public ?string $image_url = null;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
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
        'popular',
        'recommended',
        'views',
        'description',
        'created_by',
        'created_at',
    ];

    protected $appends = ['vehicle_image_url'];
    protected $table = "vehicle_info";

    /**
     * @return BelongsTo<\Modules\CarInfo\Models\Cartype, \Modules\CarInfo\Models\VehicleInfo>
     */
    public function carType(): BelongsTo
    {
        /** @var BelongsTo<Cartype,VehicleInfo> */
        return $this->belongsTo(\Modules\CarInfo\Models\Cartype::class, 'type_id', 'id');
    }

    /**
     * @return HasMany<Review, VehicleInfo>
     */
    public function reviews(): HasMany
    {
        /** @var HasMany<Review,VehicleInfo> */
        return $this->hasMany(Review::class, 'vehicle_id');
    }

    /**
     * @return BelongsTo<Brand, VehicleInfo>
     */
    public function brand(): BelongsTo
    {
        /** @var BelongsTo<Brand,VehicleInfo> */
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * @return BelongsTo<Category, VehicleInfo>
     */
    public function category(): BelongsTo
    {
        /** @var BelongsTo<Category,VehicleInfo> */
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * @return BelongsTo<Location, VehicleInfo>
     */
    public function mainLocation(): BelongsTo
    {
        /** @var BelongsTo<Location,VehicleInfo> */
        return $this->belongsTo(Location::class, 'main_location_id');
    }
    /**
     * @return BelongsTo<CarColor, VehicleInfo>
     */
    public function color(): BelongsTo
    {
        /** @var BelongsTo<CarColor,VehicleInfo> */
        return $this->belongsTo(CarColor::class, 'color_id');
    }
    /**
     * @return BelongsTo<CarFuel, VehicleInfo>
     */
    public function fuel_type(): BelongsTo
    {
        /** @var BelongsTo<CarFuel,VehicleInfo> */
        return $this->belongsTo(CarFuel::class, 'fuel_type_id');
    }
    /**
     * @return BelongsTo<Transmission, VehicleInfo>
     */
    public function transmission(): BelongsTo
    {
        /** @var BelongsTo<Transmission,VehicleInfo> */
        return $this->belongsTo(Transmission::class, 'transmission_id');
    }

    public function getVehicleImageUrlAttribute(): ?string
    {
        return $this->vehicle_image ? asset('storage/' . $this->vehicle_image) : null;
    }
    /**
     * @return HasMany<VehicleFaq, VehicleInfo>
     */
    public function faqs(): HasMany
    {
        /** @var HasMany<VehicleFaq,VehicleInfo> */
        return $this->hasMany(VehicleFaq::class, 'vehicle_id', 'id');
    }
    /**
     * @return HasMany<VehicleDamage, VehicleInfo>
     */
    public function damages(): HasMany
    {
        /** @var HasMany<VehicleDamage,VehicleInfo> */
        return $this->hasMany(VehicleDamage::class, 'vehicle_id', 'id');
    }
    /**
     * @return HasMany<VehicleTarrif, VehicleInfo>
     */
    public function tariffs(): HasMany
    {
        /** @var HasMany<VehicleTarrif,VehicleInfo> */
        return $this->hasMany(VehicleTarrif::class, 'vehicle_id', 'id');
    }
    /**
     * @return HasMany<VehicleSeason, VehicleInfo>
     */
    public function seasonals(): HasMany
    {
        /** @var HasMany<VehicleSeason,VehicleInfo> */
        return $this->hasMany(VehicleSeason::class, 'vehicle_id', 'id');
    }
    /**
     * @return HasMany<VehicleExtraService, VehicleInfo>
     */
    public function extraservices(): HasMany
    {
        /** @var HasMany<VehicleExtraService,VehicleInfo> */
        return $this->hasMany(VehicleExtraService::class, 'vehicle_id', 'id');
    }
    /**
     * @return HasMany<Maintenance, VehicleInfo>
     */
    public function maintenances(): HasMany
    {
        /** @var HasMany<Maintenance,VehicleInfo> */
        return $this->hasMany(Maintenance::class, 'vehicle_id');
    }
    /**
     * @return BelongsTo<User, VehicleInfo>
     */
    public function owner(): BelongsTo
    {
        /** @var BelongsTo<User,VehicleInfo> */
        return $this->belongsTo(User::class, 'created_by');
    }
}
