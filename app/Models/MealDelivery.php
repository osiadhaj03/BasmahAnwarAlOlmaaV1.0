<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meal_id',
        'delivered_by',
        'subscription_id',
        'delivery_date',
        'meal_type',
        'status',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivered_at' => 'datetime',
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
     * الوجبة
     */
    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }

    /**
     * الطباخ المسلّم
     */
    public function deliveredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    /**
     * الاشتراك
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(KitchenSubscription::class, 'subscription_id');
    }

    // Accessors

    /**
     * ترجمة نوع الوجبة
     */
    public function getMealTypeArabicAttribute(): string
    {
        return match($this->meal_type) {
            'breakfast' => 'فطور',
            'lunch' => 'غداء',
            'dinner' => 'عشاء',
            default => $this->meal_type,
        };
    }

    /**
     * ترجمة الحالة
     */
    public function getStatusArabicAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'delivered' => 'تم التسليم',
            'missed' => 'فائت',
            default => $this->status,
        };
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeMissed($query)
    {
        return $query->where('status', 'missed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('delivery_date', today());
    }
}
