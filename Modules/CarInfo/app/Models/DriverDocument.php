<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $driver_id
 * @property string|array<string>|null $document
 */
class DriverDocument extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'driver_id',
        'document'
    ];

    /**
     * @return BelongsTo<Driver, DriverDocument>
     */
    public function driver(): BelongsTo
    {
        /** @var BelongsTo<Driver, DriverDocument> */
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
