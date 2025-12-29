<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentInvoiceAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'invoice_id',
        'amount_allocated',
    ];

    protected $casts = [
        'amount_allocated' => 'decimal:2',
    ];

    /**
     * الدفعة
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(KitchenPayment::class, 'payment_id');
    }

    /**
     * الفاتورة
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(KitchenInvoice::class, 'invoice_id');
    }
}
