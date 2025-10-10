<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *  @property int|null $user_id
 *  @property string|null $device_type
 *  @property string|null $browser
 *  @property string|null $os
 *  @property string|null $ip_address
 *  @property string|null $location
 *  @property \Illuminate\Support\Carbon|null $created_at
 *  @property \Illuminate\Support\Carbon|null $updated_at
 */
class UserDevice extends Model
{
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'device_type',
        'browser',
        'os',
        'ip_address',
        'location',
    ];
}
