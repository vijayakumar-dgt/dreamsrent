<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\LocationFactory;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): LocationFactory
    // {
    //     // return LocationFactory::new();
    // }

    protected static function booted()
    {
        static::deleting(function ($location) {
            $location->workingDays()->delete();
        });
    }

    public function workingDays()
    {
        return $this->hasMany(LocationWorkingDay::class);
    }
}
