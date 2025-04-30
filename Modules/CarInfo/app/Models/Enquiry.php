<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use HasFactory;
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
