<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\CylinderFactory;
/**
 * @property int $id
 * @property string $cylinder_type
 * @property int|null $language_id
 * @property int $status
 */
class Cylinder extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): CylinderFactory
    // {
    //     // return CylinderFactory::new();
    // }
}
