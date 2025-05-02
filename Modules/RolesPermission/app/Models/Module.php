<?php

namespace Modules\RolesPermission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// use Modules\RolesPermission\Database\Factories\ModuleFactory;

class Module extends Model
{
     /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

     /**
     * @return BelongsTo<Module, Module>
     */
    public function parentModule(): BelongsTo
    {
        /** @var belongsTo<Module, Module> */
        return $this->belongsTo(Module::class, 'parent_id', 'id');
    }

     /**
     * @return HasMany<Module, Module>
     */
    public function childModules(): HasMany
    {
        /** @var hasMany<Module, Module> */
        return $this->hasMany(Module::class, 'parent_id', 'id');
    }
    /**
     * @return HasMany<Permission, Module>
     */
    public function permissions(): HasMany
    {
          /** @var hasMany<Permission, Module> */
        return $this->hasMany(Permission::class, 'module_id', 'id');
    }
}
