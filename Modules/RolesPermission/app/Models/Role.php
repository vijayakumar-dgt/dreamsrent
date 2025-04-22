<?php

namespace Modules\RolesPermission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\RolesPermission\Database\Factories\RoleFactory;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'role_name',
        'status',
    ];

    public static $roleSecretKey = 'RoleId';

}
