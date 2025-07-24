<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    // If your table name is not 'invoice_items', uncomment and set it
    // protected $table = 'invoice_items';

    // Only include fields you want to mass-assign
    protected $fillable = [
        'invoice_id',
        'description',
        'qty',
        'price',
        'tax',
        'total_price',
    ];

    /**
     * Get the invoice that owns the item.
     *
     * @return BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
