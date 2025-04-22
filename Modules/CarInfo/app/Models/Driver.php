<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\DriverFactory;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'driver_name',
        'image',
        'gender',
        'phone_number',
        'address',
        'card_number',
        'email',
        'assigned_cars',
        'date_of_issue',
        'valid_date',
    ];

    public function documents()
    {
        return $this->hasMany(DriverDocument::class, 'driver_id');
    }

}
