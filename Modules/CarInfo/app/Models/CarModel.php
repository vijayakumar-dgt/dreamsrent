<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\CarModelFactory;

class CarModel extends Model
{
    use HasFactory;
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

    public function brands()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
