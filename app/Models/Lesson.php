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
        'lesson_date',
        'start_time',
        'end_time',
        'location',
        'status',
        'max_students',
        'notes',
    ];

    protected $casts = [
        'lesson_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'max_students' => 'integer',
    ];

    // العلاقات
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
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
    public function getFullDateTimeAttribute()
    {
        return $this->lesson_date->format('Y-m-d') . ' ' . $this->start_time . ' - ' . $this->end_time;
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
        return $query->where('lesson_date', '>=', now()->toDateString());
    }

    public function scopeToday($query)
    {
        return $query->where('lesson_date', now()->toDateString());
    }
}
