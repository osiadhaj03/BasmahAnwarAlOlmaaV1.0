<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, HasApiTokens, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'phone',
        'student_id',
        'academic_level',
        'employee_id',
        'specialization',
        'hire_date',
        'bio',
        'birth_date',
        'gender',
        'address',
        'nationality',
        'is_active',
        'last_login_at',
        'avatar_url',
        'two_factor_confirmed_at',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'hire_date' => 'date',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
    ];

    // Filament Access Control
    public function canAccessPanel(Panel $panel): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Admin panel - للمدراء والمعلمين فقط
        if ($panel->getId() === 'admin') {
            return in_array($this->type, ['admin', 'teacher']);
        }

        // Student panel - للطلاب فقط
        if ($panel->getId() === 'student') {
            return $this->type === 'student';
        }

        return false;
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

    // Filament Avatar Support
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
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
