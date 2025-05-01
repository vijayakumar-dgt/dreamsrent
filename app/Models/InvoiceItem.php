<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Invoice;

class InvoiceItem extends Model
{

    protected $fillable = [
        'invoice_id',
        'description',
        'qty',
        'price',
        'tax',
        'total_price',
        'created_at',
        'updated_at',
    ];

    /**
     * @return BelongsTo<Invoice, InvoiceItem>
     */
    public function invoice(): BelongsTo
    {
        /** @var BelongsTo<Invoice, InvoiceItem> */
        return $this->belongsTo(Invoice::class);
    }
}
