<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\ExtraServiceFactory;

/**
 *  @property int $id
 *  @property string $name
 *  @property string|array<string>|null $icon
 *  @property string|array<string>|null $image
 *  @property string $description
 *  @property int $status
 *  @property int $language_id
 *  @property \Illuminate\Support\Carbon $created_at
 *  @property \Illuminate\Support\Carbon $updated_at
 *  @property \Illuminate\Support\Carbon $deleted_at
 * @property float|null $price      // Dynamically added
 * @property string|null $value     // Dynamically added
 *  @property double $price
 */
class ExtraService extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ["name", "language_id", "icon", "description", "image"];

    // protected static function newFactory(): ExtraServiceFactory
    // {
    //     // return ExtraServiceFactory::new();
    // }
}
