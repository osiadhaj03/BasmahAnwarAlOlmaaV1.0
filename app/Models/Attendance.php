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
}
