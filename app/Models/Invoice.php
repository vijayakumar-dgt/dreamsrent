<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceItem[] $items
 */
class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'car_id',
        'currency_id',
        'status',
        'biller',
        'customer_id',
        'payment_method',
        'terms',
        'notes',
        'subtotal',
        'tax',
        'grand_total',
        'created_at',
        'updated_at',
        'deleted_at',
        'from_date',
        'to_date',
        'language_id'
    ];

    /**
     * Get all the invoice items for the invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }
}
