<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\InvoiceItem;

/**
 * @property InvoiceItem $InvoiceItem
 */
class Invoice extends Authenticatable
{
    use Notifiable;
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
        'to_date'
    ];

    /**
     * Get all the invoice items for the invoice.
     *
     * @return HasMany<InvoiceItem, Invoice>
     */
    public function items(): HasMany
    {
         /** @var hasMany<InvoiceItem, Invoice> */
        return $this->hasMany(InvoiceItem::class, 'invoice_id'); // explicitly tell Laravel the FK
    }
}
