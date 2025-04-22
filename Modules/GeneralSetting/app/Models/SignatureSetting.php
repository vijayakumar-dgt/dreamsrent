<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\GeneralSetting\Database\Factories\SignatureSettingFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SignatureSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'signature_name',
        'signature_image',
        'status',
        'is_default',
    ];
}
