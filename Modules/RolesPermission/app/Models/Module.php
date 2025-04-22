<?php

namespace Modules\RolesPermission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\RolesPermission\Database\Factories\ModuleFactory;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // Parent relation (a module belongs to a parent)
    public function parentModule()
    {
        return $this->belongsTo(Module::class, 'parent_id', 'id');
    }

    // Child relation (a module has many children)
    public function childModules()
    {
        return $this->hasMany(Module::class, 'parent_id', 'id');
    }

    // One module has many permissions
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'module_id', 'id');
    }



}
