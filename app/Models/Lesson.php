<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'teacher_id',
        'lesson_section_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'lesson_days',
        'location_type',
        'location_details',
        'meeting_link',
        'is_recurring',
        'status',
        'max_students',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'lesson_days' => 'array',
        'is_recurring' => 'boolean',
        'max_students' => 'integer',
    ];

    // العلاقات
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lessonSection()
    {
        return $this->belongsTo(LessonSection::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'lesson_student', 'lesson_id', 'student_id')
                    ->withPivot('enrolled_at', 'enrollment_status', 'notes')
                    ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceCodes()
    {
        return $this->hasMany(AttendanceCode::class);
    }

    public function activeAttendanceCodes()
    {
        return $this->hasMany(AttendanceCode::class)->where('is_active', true);
    }

    // Helper methods
    public function getLessonDaysArabicAttribute()
    {
        $daysMap = [
            'sunday' => 'الأحد',
            'monday' => 'الاثنين',
            'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
            'thursday' => 'الخميس',
            'friday' => 'الجمعة',
            'saturday' => 'السبت',
        ];

        if (!$this->lesson_days || !is_array($this->lesson_days)) {
            return '';
        }

        $arabicDays = array_map(function($day) use ($daysMap) {
            return $daysMap[strtolower($day)] ?? $day;
        }, $this->lesson_days);

        return implode(', ', $arabicDays);
    }

    public function getFullDateTimeAttribute()
    {
        $dateRange = $this->start_date->format('Y-m-d');
        if ($this->end_date && $this->end_date != $this->start_date) {
            $dateRange .= ' إلى ' . $this->end_date->format('Y-m-d');
        }
        return $dateRange . ' ' . $this->start_time . ' - ' . $this->end_time;
    }

    public function getDurationAttribute()
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        return $start->diffInMinutes($end);
    }

    public function getEnrolledStudentsCountAttribute()
    {
        return $this->students()->wherePivot('enrollment_status', 'enrolled')->count();
    }

    public function getAttendanceRateAttribute()
    {
        $totalStudents = $this->enrolledStudentsCount;
        if ($totalStudents === 0) return 0;
        
        $presentStudents = $this->attendances()->where('status', 'present')->count();
        return round(($presentStudents / $totalStudents) * 100, 2);
    }

    public function canEnrollMoreStudents()
    {
        return $this->max_students === null || $this->enrolledStudentsCount < $this->max_students;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_date', '<=', now()->toDateString())
                     ->whereDate('end_date', '>=', now()->toDateString());
    }

    public function scopeOnline($query)
    {
        return $query->where('location_type', 'online');
    }

    public function scopeOffline($query)
    {
        return $query->where('location_type', 'offline');
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }
}
