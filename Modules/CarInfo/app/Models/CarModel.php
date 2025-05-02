<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\CarModelFactory;

class CarModel extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'language_id',
        'model_name',
        'brand_id',
        'total_cars',
        'status'
    ];
    /**
     * @return BelongsTo<Brand, CarModel> */
    public function brands(): BelongsTo
    {
        /** @var BelongsTo<Brand, CarModel> */
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
