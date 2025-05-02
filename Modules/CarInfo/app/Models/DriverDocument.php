<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\CarInfo\Database\Factories\DriverDocumentFactory;

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
