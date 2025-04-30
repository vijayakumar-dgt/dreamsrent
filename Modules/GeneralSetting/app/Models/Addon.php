<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\GeneralSetting\Database\Factories\AddonFactory;

class Addon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

     protected $table = 'addons';

     protected $fillable = [
        'name',
        'status',
        'slug',
        'version',
        'price',
        'created_at',
        'updated_at',
        'deleted_at'
     ];
    // protected static function newFactory(): AddonFactory
    // {
    //     // return AddonFactory::new();
    // }
}
