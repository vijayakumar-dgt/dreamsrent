<?php

namespace Modules\RolesPermission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends Model
{
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

    /**
     * @return BelongsTo<Module, Permission>
     */
    public function module(): BelongsTo
    {
        /** @var belongsTo<Module, Permission> */
        return $this->belongsTo(Module::class, 'module_id');
    }

    /**
     *
     * @return BelongsTo<Module, Permission>
     */
    public function role(): BelongsTo
    {
        /** @var belongsTo<Module, Permission> */
        return $this->belongsTo(Role::class);
    }
}
