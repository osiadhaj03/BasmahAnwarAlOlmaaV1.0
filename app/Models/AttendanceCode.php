<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttendanceCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'code',
        'expires_at',
        'is_active',
        'usage_count',
        'max_usage',
        'created_by',
        'deactivated_at',
        'deactivated_by',
        'notes',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'max_usage' => 'integer',
        'deactivated_at' => 'datetime',
    ];

    // العلاقات
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deactivatedBy()
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }

    // Helper methods
    public static function generateUniqueCode($length = 6)
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (self::where('code', $code)->where('is_active', true)->exists());

        return $code;
    }

    public function isExpired()
    {
        return $this->expires_at < now();
    }

    public function isUsageLimitReached()
    {
        return $this->max_usage !== null && $this->usage_count >= $this->max_usage;
    }

    public function canBeUsed()
    {
        return $this->is_active && !$this->isExpired() && !$this->isUsageLimitReached();
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
        
        if ($this->isUsageLimitReached()) {
            $this->update(['is_active' => false]);
        }
    }

    public function deactivate($userId = null, $reason = null)
    {
        $this->update([
            'is_active' => false,
            'deactivated_at' => now(),
            'deactivated_by' => $userId,
            'notes' => $reason ? $this->notes . "\nتم إلغاء التفعيل: " . $reason : $this->notes,
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
                    ->where('expires_at', '>', now());
    }

    public function scopeByLesson($query, $lessonId)
    {
        return $query->where('lesson_id', $lessonId);
    }
}
