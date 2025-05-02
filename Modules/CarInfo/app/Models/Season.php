<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;

// use Modules\CarInfo\Database\Factories\SeasonFactory;
/**
 * @property int $id
 * @property string $name
 * @property int $status
 */
class Season extends Model
{

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): SeasonFactory
    // {
    //     // return SeasonFactory::new();
    // }
}
