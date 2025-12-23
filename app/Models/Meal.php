<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'kitchen_id',
        'name',
        'description',
        'meal_type',
        'image',
    ];

    // العلاقات

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
        return $this->hasMany(MealDelivery::class);
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

    // Scopes

    public function scopeBreakfast($query)
    {
        return $query->where('meal_type', 'breakfast');
    }

    public function scopeLunch($query)
    {
        return $query->where('meal_type', 'lunch');
    }

    public function scopeDinner($query)
    {
        return $query->where('meal_type', 'dinner');
    }
}
