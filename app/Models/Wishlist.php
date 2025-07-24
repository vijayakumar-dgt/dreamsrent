<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CarInfo\Models\VehicleInfo;

class Wishlist extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wishlists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'vehicle_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Get the vehicle associated with the wishlist.
     *
     * @return BelongsTo<VehicleInfo, Wishlist>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(VehicleInfo::class, 'vehicle_id');
    }

    /**
     * Get the user that owns the wishlist.
     *
     * @return BelongsTo<User, Wishlist>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
