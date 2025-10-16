<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'student_id',
        'status',
        'attendance_date',
        'used_code',
        'attendance_method',
        'notes',
        'marked_at',
        'marked_by',
    ];

    protected $casts = [
        'attendance_date' => 'datetime',
        'marked_at' => 'datetime',
    ];

    // العلاقات
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    // Helper methods
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'present' => 'حاضر',
            'absent' => 'غائب',
            'late' => 'متأخر',
            'excused' => 'معذور',
            default => 'غير محدد'
        };
    }

    public function getMethodLabelAttribute()
    {
        return match($this->attendance_method) {
            'code' => 'كود عشوائي',
            'manual' => 'يدوي',
            'qr' => 'QR Code',
            default => 'غير محدد'
        };
    }

    // Scopes
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeByLesson($query, $lessonId)
    {
        return $query->where('lesson_id', $lessonId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // وظائف مساعدة إضافية
    public function isLate()
    {
        return $this->status === 'late';
    }

    public function isPresent()
    {
        return $this->status === 'present';
    }

    public function isAbsent()
    {
        return $this->status === 'absent';
    }

    public function isExcused()
    {
        return $this->status === 'excused';
    }

    public function wasMarkedByCode()
    {
        return $this->attendance_method === 'code' && !empty($this->used_code);
    }

    public function getTimeDifferenceFromLessonStart()
    {
        if (!$this->lesson || !$this->attendance_date) {
            return null;
        }

        $lessonDateTime = $this->lesson->getNextLessonDateTime();
        if (!$lessonDateTime) {
            return null;
        }

        return $this->attendance_date->diffInMinutes($lessonDateTime, false);
    }

    // التحقق من صحة وقت التسجيل
    public function isValidAttendanceTime()
    {
        if (!$this->lesson || !$this->attendance_date) {
            return false;
        }

        return $this->lesson->isValidLessonDateTime($this->attendance_date);
    }

    // تحديد الحالة تلقائياً بناءً على وقت التسجيل
    public function autoSetStatus()
    {
        if (!$this->lesson || !$this->attendance_date) {
            return;
        }

        $suggestedStatus = $this->lesson->getAttendanceStatus($this->attendance_date);
        if ($suggestedStatus && $this->status !== $suggestedStatus) {
            $this->status = $suggestedStatus;
        }
    }
}
