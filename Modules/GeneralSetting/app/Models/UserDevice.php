<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\GeneralSetting\Database\Factories\UserDeviceFactory;

/** @property int $user_id
 *  @property string|null $device_type
 *  @property string|null $browser
 *  @property string|null $os
 *  @property string|null $ip_address
 *  @property string|null $location
 */
class UserDevice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): UserDeviceFactory
    // {
    //     // return UserDeviceFactory::new();
    // }
}
