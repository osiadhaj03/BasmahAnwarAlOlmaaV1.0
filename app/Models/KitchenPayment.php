<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'subscription_id',
        'amount',
        'payment_date',
        'collected_by',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    // العلاقات

    /**
     * الفاتورة
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(KitchenInvoice::class, 'invoice_id');
    }

    /**
     * الاشتراك
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(KitchenSubscription::class, 'subscription_id');
    }

    /**
     * المحصّل
     */
    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    // Accessors

    /**
     * ترجمة طريقة الدفع
     */
    public function getPaymentMethodArabicAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'نقداً',
            'bank_transfer' => 'تحويل بنكي',
            default => $this->payment_method,
        };
    }
}
