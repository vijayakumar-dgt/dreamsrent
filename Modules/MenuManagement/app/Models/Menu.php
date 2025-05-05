<?php

namespace Modules\MenuManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string|null $menus
 * @property array<int, mixed>|null $menus_array
 * @property array<int, mixed>|null $parsed_menus
 */
class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'menu_type',
        'permenantlink',
        'menus',
        'status',
        'language_id'
    ];

    /**
     * @var array<int, string>
     */
    protected $dates = ['deleted_at'];
}
