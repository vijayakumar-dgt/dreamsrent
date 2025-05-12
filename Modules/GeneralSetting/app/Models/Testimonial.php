<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string|null $image
 * @property string|null $review
 * @property string|null $customer_name
 * @property int|null $ratings
 * @property string|null $status
 * @property string|null $testimonial_image
 * @property string|null $created_date
 * @property string|null $created_at
 */
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
