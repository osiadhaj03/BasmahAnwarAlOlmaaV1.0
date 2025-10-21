<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LessonSectionStudent extends Model
{
    protected $table = 'lesson_section_student';

    protected $fillable = [
        'lesson_section_id',
        'student_id',
        'enrolled_at',
        'enrollment_status',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'enrollment_status' => 'string',
    ];

    // العلاقة مع قسم الدورة
    public function lessonSection(): BelongsTo
    {
        return $this->belongsTo(LessonSection::class);
    }

    // العلاقة مع الطالب
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Accessor للحصول على حالة التسجيل بالعربية
    public function getEnrollmentStatusArabicAttribute(): string
    {
        return match ($this->enrollment_status) {
            'active' => 'نشط',
            'dropped' => 'متوقف',
            'completed' => 'مكتمل',
            default => 'غير محدد',
        };
    }

    // Scope للحصول على التسجيلات النشطة
    public function scopeActive($query)
    {
        return $query->where('enrollment_status', 'active');
    }

    // Scope للحصول على التسجيلات حسب القسم
    public function scopeBySection($query, $sectionId)
    {
        return $query->where('lesson_section_id', $sectionId);
    }

    // Scope للحصول على التسجيلات حسب الطالب
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}
