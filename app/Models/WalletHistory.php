<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class WalletHistory
 *
 * @property int $id
 * @property int $user_id
 * @property float $amount
 * @property string $payment_type
 * @property string $status
 * @property string $reference_id
 * @property string $type
 * @property string $transaction_id
 * @property string $transaction_date
 * @property \Illuminate\Support\Carbon $deleted_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class WalletHistory extends Model
{
    use SoftDeletes;

    protected $table = 'wallet_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transaction_date' => 'datetime',
        'deleted_at' => 'datetime',
        'amount' => 'float',
    ];

    // Example constants for status and type (customize as needed)
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    
    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    /**
     * Get the user that owns the wallet history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
