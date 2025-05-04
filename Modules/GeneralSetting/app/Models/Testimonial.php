<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_name',
        'review',
        'image',
        'ratings',
        'location',
        'status',
        'order_by',
    ];
}
