<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $vehicle_id
 * @property int $extra_service_id
 * @property string $value
 * @property float $price
 * @property string $created_at
 * @property string $updated_at
 * @property \Modules\CarInfo\Models\ExtraService|null $extraService
 */

class VehicleExtraService extends Model
{

    /**
     * @var list<string>
     */
    protected $fillable = ['vehicle_id', 'extra_service_id', 'value', 'price'];

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
