<?php

namespace Modules\GeneralSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string|null $symbol
 * @property string $currency_name
 * @property string $code
 * @property float $exchange_rate
 * @property int $status
 */
class Currency extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['currency_name', 'code', 'symbol', 'exchange_rate', 'status'];
    protected $table = 'currencies';
}
