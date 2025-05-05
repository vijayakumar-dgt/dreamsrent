<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CarModel
 *
 * @property int $id
 * @property int|null $language_id
 * @property string $model_name
 * @property int $brand_id
 * @property int $total_cars
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Brand $brand
 */
class CarModel extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'language_id',
        'model_name',
        'brand_id',
        'total_cars',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the brand associated with the car model.
     *
     * @return BelongsTo<Brand, CarModel>
     */
    public function brand(): BelongsTo
    {
        /** @var BelongsTo<Brand, CarModel> */
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
