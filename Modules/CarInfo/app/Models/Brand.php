<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\BrandFactory;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
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

    public function carModels()
    {
        return $this->hasMany(CarModel::class, 'brand_id');
    }
}
