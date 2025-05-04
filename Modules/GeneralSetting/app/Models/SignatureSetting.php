<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SignatureSetting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'signature_name',
        'signature_image',
        'status',
        'is_default',
    ];
}
