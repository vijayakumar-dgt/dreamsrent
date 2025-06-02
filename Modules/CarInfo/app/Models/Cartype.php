<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property int|null $language_id
 * @property int $status
 * @property string|null $icon
 * @property int $car_count
 * @property string|null $image_url
 */
class Cartype extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'category_id',
        'language_id',
        'status',
        'icon',
    ];
}
