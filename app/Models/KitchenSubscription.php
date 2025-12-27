<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitchenSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kitchen_id',
        'start_date',
        'end_date',
        'status',
        'monthly_price',
        'number_meal',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_price' => 'decimal:2',
    ];

    // العلاقات

    /**
     * الزبون
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * المطبخ
     */
    public function kitchen(): BelongsTo
    {
        return $this->belongsTo(Kitchen::class);
    }

    /**
     * سجلات التسليم
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(MealDelivery::class, 'subscription_id');
    }

    /**
     * الفواتير
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(KitchenInvoice::class, 'subscription_id');
    }

    /**
     * الدفعات
     */
    public function payments(): HasMany
    {
        return $this->hasMany(KitchenPayment::class, 'subscription_id');
    }

    /**
     * حساب الرصيد
     * موجب = للمشترك رصيد زائد
     * سالب = عليه متأخرات
     */
    public function getBalanceAttribute(): float
    {
        $totalInvoices = $this->invoices()->sum('amount');
        $totalPayments = $this->payments()->sum('amount');
        
        return (float) ($totalPayments - $totalInvoices);
    }

    /**
     * مجموع الفواتير
     */
    public function getTotalInvoicesAttribute(): float
    {
        return (float) $this->invoices()->sum('amount');
    }

    /**
     * مجموع الدفعات
     */
    public function getTotalPaymentsAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    // Accessors

    /**
     * ترجمة الحالة
     */
    public function getStatusArabicAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'paused' => 'موقوف',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    /**
     * هل الاشتراك نشط؟
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
