<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\CartypeFactory;

/**
 * @property int $id
 * @property string $name
 * @property int|null $language_id
 * @property int $status
 * @property string|null $icon
 */
class Cartype extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): CartypeFactory
    // {
    //     // return CartypeFactory::new();
    // }
}
