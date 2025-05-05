<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $vehicle_id
 * @property string $question
 * @property string $answer
 * @property string $created_at
 * @property string $updated_at
 */
class VehicleFaq extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'vehicle_faqs';
    protected $fillable = [ 'vehicle_id', 'question', 'answer'  ];
}
