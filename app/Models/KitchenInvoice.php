<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'user_id',
        'invoice_number',
        'amount',
        'billing_date',
        'due_date',
        'status',
        'collected_by',
        'received_from',
        'paid_at',
        'payment_method',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billing_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    // العلاقات

    /**
     * الاشتراك
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(KitchenSubscription::class, 'subscription_id');
    }

    /**
     * الزبون
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * المستلم (طباخ أو مدير)
     */
    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    // Accessors

    /**
     * ترجمة الحالة
     */
    public function getStatusArabicAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'paid' => 'مدفوعة',
            'overdue' => 'متأخرة',
            'cancelled' => 'ملغية',
            default => $this->status,
        };
    }

    /**
     * ترجمة طريقة الدفع
     */
    public function getPaymentMethodArabicAttribute(): ?string
    {
        if (!$this->payment_method) return null;

        return match($this->payment_method) {
            'cash' => 'نقداً',
            'bank_transfer' => 'تحويل بنكي',
            default => $this->payment_method,
        };
    }

    /**
     * هل الفاتورة مدفوعة؟
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * هل الفاتورة متأخرة؟
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'paid' && $this->due_date < today();
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pending', 'overdue']);
    }

    // Methods

    /**
     * توليد رقم فاتورة جديد
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ym');
        $lastInvoice = static::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
