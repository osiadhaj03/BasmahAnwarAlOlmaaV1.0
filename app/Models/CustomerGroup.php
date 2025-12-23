<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'kitchen_id',
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
     * أعضاء المجموعة (الزبائن)
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'customer_group_members', 'group_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * سجلات أعضاء المجموعة
     */
    public function memberRecords(): HasMany
    {
        return $this->hasMany(CustomerGroupMember::class, 'group_id');
    }

    /**
     * عدد الأعضاء
     */
    public function getMembersCountAttribute(): int
    {
        return $this->members()->count();
    }
}
