<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\DamageTypeFactory;

class DamageType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): DamageTypeFactory
    // {
    //     // return DamageTypeFactory::new();
    // }
}
