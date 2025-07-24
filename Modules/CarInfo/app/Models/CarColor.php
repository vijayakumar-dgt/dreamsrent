<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CarColor
 *
 * @property int $id
 * @property string $name
 * @property int $language_id
 * @property string $value
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CarColor extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["name", "language_id", "value", "status"];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'language_id' => 'integer',
        'status' => 'integer',
    ];
}
