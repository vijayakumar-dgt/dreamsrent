<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\CarInfo\Database\Factories\VehicleExtraServiceFactory;

class VehicleExtraService extends Model
{

    /**
     * @var list<string>
     */
    protected $fillable = ['vehicle_id', 'extra_service_id', 'value', 'price'];

    // protected static function newFactory(): VehicleExtraServiceFactory
    // {
    //     // return VehicleExtraServiceFactory::new();
    // }

    /**
     * @return BelongsTo<ExtraService, VehicleExtraService>
     */
    public function extraService(): BelongsTo
    {
        /**
         * @var BelongsTo<ExtraService, VehicleExtraService>
         */
        return $this->belongsTo(ExtraService::class, 'extra_service_id', 'id');
    }
}
