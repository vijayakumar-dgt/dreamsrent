<?php

namespace Modules\RolesPermission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $encrypted_role_id
 * @property \Carbon\Carbon $created_at
 * @property string $created_date
 */

class Role extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'role_name',
        'created_by',
        'status',
    ];
     /**
     * @var string
     */
    public static $roleSecretKey = 'RoleId';
}
