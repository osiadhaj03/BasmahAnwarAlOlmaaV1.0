<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'phone',
        'student_id',
        'employee_id',
        'bio',
        'department',
        'birth_date',
        'gender',
        'address',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Filament Access Control
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && in_array($this->type, ['admin', 'teacher']);
    }

    // العلاقات
    public function teacherLessons()
    {
        return $this->hasMany(Lesson::class, 'teacher_id');
    }

    public function studentLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_student', 'student_id', 'lesson_id')
                    ->withPivot('enrolled_at', 'enrollment_status', 'notes')
                    ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function createdAttendanceCodes()
    {
        return $this->hasMany(AttendanceCode::class, 'created_by');
    }

    public function deactivatedAttendanceCodes()
    {
        return $this->hasMany(AttendanceCode::class, 'deactivated_by');
    }

    public function markedAttendances()
    {
        return $this->hasMany(Attendance::class, 'marked_by');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->type === 'admin';
    }

    public function isTeacher()
    {
        return $this->type === 'teacher';
    }

    public function isStudent()
    {
        return $this->type === 'student';
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
