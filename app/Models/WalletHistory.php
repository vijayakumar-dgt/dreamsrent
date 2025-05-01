<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletHistory extends Model
{
    use SoftDeletes;

    protected $table = 'wallet_history';

    protected $fillable = [
        'user_id',
        'amount',
        'payment_type',
        'status',
        'reference_id',
        'type',
        'transaction_id',
        'transaction_date',
    ];

    /**
     * @var array<int, string>
     */
    protected $dates = ['deleted_at'];
}
