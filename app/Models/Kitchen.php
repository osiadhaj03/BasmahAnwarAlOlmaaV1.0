<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kitchen extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // العلاقات

    /**
     * الطباخين العاملين في هذا المطبخ
     */
    public function cooks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kitchen_cooks', 'kitchen_id', 'user_id')
            ->withPivot('specialty', 'is_active')
            ->withTimestamps();
    }

    /**
     * سجلات الطباخين
     */
    public function kitchenCooks(): HasMany
    {
        return $this->hasMany(KitchenCook::class);
    }

    /**
     * الوجبات المتاحة في هذا المطبخ
     */
    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    /**
     * مصروفات المطبخ
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(KitchenExpense::class);
    }

    /**
     * الاشتراكات في هذا المطبخ
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(KitchenSubscription::class);
    }

    /**
     * مجموعات الزبائن
     */
    public function customerGroups(): HasMany
    {
        return $this->hasMany(CustomerGroup::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
