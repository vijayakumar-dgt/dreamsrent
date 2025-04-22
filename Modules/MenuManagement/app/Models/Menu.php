<?php

namespace Modules\MenuManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\MenuManagement\Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'menu_type',
        'permenantlink',
        'menus',
        'status',
        'language_id'
    ];

    protected $dates = ['deleted_at'];
}
