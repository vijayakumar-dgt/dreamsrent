<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Brand
 *
 * @property int $id
 * @property int|null $language_id
 * @property string|null $brand_image
 * @property string|null $brand_icon
 * @property string $brand_name
 * @property int $total_cars
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Brand extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'language_id',
        'brand_image',
        'brand_icon',
        'brand_name',
        'total_cars',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @return HasMany<CarModel,Brand>
     */
    public function carModels(): HasMany
    {
         /** @var HasMany<CarModel, Brand>*/
        return $this->hasMany(CarModel::class, 'brand_id');
    }
}
