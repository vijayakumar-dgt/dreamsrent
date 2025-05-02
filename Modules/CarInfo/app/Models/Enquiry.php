<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $car_id
 * @property string $customer_name
 * @property string $email
 * @property string $phone
 * @property string $enquiry_date
 * @property string $enquiry_details
 * @property int $status
 * @property string $comment
 */
class Enquiry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'car_id',
        'customer_name',
        'email',
        'phone',
        'enquiry_date',
        'enquiry_details',
        'status'
    ];
}
