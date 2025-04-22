<?php

namespace Modules\RolesPermission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\RolesPermission\Database\Factories\PermissionFactory;

class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'role_id',
        'module_id',
        'create',
        'edit',
        'delete',
        'view',
        'allow_all',
        'created_by',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

}
