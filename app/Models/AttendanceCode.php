<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttendanceCode extends Model
{
    use HasFactory;

    // ثوابت النظام
    const DEFAULT_REFRESH_INTERVAL = 30; // 30 ثانية
    const DEFAULT_CODE_LENGTH = 6;
    const MAX_REFRESH_INTERVAL = 300; // 5 دقائق
    const MIN_REFRESH_INTERVAL = 10; // 10 ثواني

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
        'auto_refresh',
        'refresh_interval',
        'display_started_at',
        'last_refreshed_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'max_usage' => 'integer',
        'deactivated_at' => 'datetime',
        'auto_refresh' => 'boolean',
        'refresh_interval' => 'integer',
        'display_started_at' => 'datetime',
        'last_refreshed_at' => 'datetime',
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

    // دوال نظام الأكواد العشوائية المتجددة
    
    /**
     * إنشاء كود مع توقيت محدد للتحديث التلقائي
     */
    public static function generateTimedCode($lessonId, $createdBy, $refreshInterval = null, $expiresAt = null)
    {
        $refreshInterval = $refreshInterval ?? self::DEFAULT_REFRESH_INTERVAL;
        $expiresAt = $expiresAt ?? now()->addHours(2); // افتراضي ساعتين
        
        return self::create([
            'lesson_id' => $lessonId,
            'code' => self::generateUniqueCode(),
            'expires_at' => $expiresAt,
            'is_active' => true,
            'created_by' => $createdBy,
            'auto_refresh' => true,
            'refresh_interval' => $refreshInterval,
            'display_started_at' => now(),
            'last_refreshed_at' => now(),
        ]);
    }

    /**
     * فحص ما إذا كان الكود يحتاج للتحديث
     */
    public function shouldRefresh()
    {
        if (!$this->auto_refresh || !$this->is_active) {
            return false;
        }

        if (!$this->last_refreshed_at) {
            return true;
        }

        $secondsSinceLastRefresh = now()->diffInSeconds($this->last_refreshed_at);
        return $secondsSinceLastRefresh >= $this->refresh_interval;
    }

    /**
     * تحديث الكود العشوائي
     */
    public function refreshCode()
    {
        if (!$this->shouldRefresh()) {
            return false;
        }

        $newCode = self::generateUniqueCode();
        
        $this->update([
            'code' => $newCode,
            'last_refreshed_at' => now(),
        ]);

        return true;
    }

    /**
     * التحقق من صحة الكود للوقت الحالي
     */
    public function isValidForCurrentTime()
    {
        return $this->is_active && 
               !$this->isExpired() && 
               !$this->isUsageLimitReached() &&
               $this->display_started_at &&
               $this->display_started_at <= now();
    }

    /**
     * التحقق من أن الكود نشط حالياً للعرض
     */
    public function isCurrentlyActive()
    {
        return $this->isValidForCurrentTime() && $this->display_started_at;
    }

    /**
     * بدء عرض الكود
     */
    public function startDisplay()
    {
        $this->update([
            'display_started_at' => now(),
            'last_refreshed_at' => now(),
            'is_active' => true,
        ]);
    }

    /**
     * إيقاف عرض الكود
     */
    public function stopDisplay($userId = null)
    {
        $this->update([
            'is_active' => false,
            'deactivated_at' => now(),
            'deactivated_by' => $userId,
        ]);
    }

    /**
     * الحصول على الوقت المتبقي للتحديث التالي (بالثواني)
     */
    public function getSecondsUntilNextRefresh()
    {
        if (!$this->auto_refresh || !$this->last_refreshed_at) {
            return 0;
        }

        $secondsSinceLastRefresh = now()->diffInSeconds($this->last_refreshed_at);
        return max(0, $this->refresh_interval - $secondsSinceLastRefresh);
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

    /**
     * الأكواد التي يتم عرضها حالياً
     */
    public function scopeCurrentlyDisplaying($query)
    {
        return $query->where('is_active', true)
                    ->whereNotNull('display_started_at')
                    ->where('display_started_at', '<=', now())
                    ->where('expires_at', '>', now());
    }

    /**
     * الأكواد التي لديها تحديث تلقائي
     */
    public function scopeAutoRefresh($query)
    {
        return $query->where('auto_refresh', true);
    }

    /**
     * الأكواد التي تحتاج للتحديث
     */
    public function scopeNeedingRefresh($query)
    {
        return $query->where('auto_refresh', true)
                    ->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('last_refreshed_at')
                          ->orWhereRaw('TIMESTAMPDIFF(SECOND, last_refreshed_at, NOW()) >= refresh_interval');
                    });
    }

    /**
     * الأكواد النشطة لدرس معين
     */
    public function scopeActiveForLesson($query, $lessonId)
    {
        return $query->where('lesson_id', $lessonId)
                    ->where('is_active', true)
                    ->where('expires_at', '>', now());
    }
}
