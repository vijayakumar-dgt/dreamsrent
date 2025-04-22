<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrivingType extends Model
{
    protected $table = 'driving_types';
    protected $fillable = ['name'];
}
