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

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            // Force new registered users to be students unless explicitly set
            if (empty($user->type)) {
                $user->type = 'student';
            }
        });
    }

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

        // Student panel - للطلاب والزبائن
        if ($panel->getId() === 'student') {
            return in_array($this->type, ['student', 'customer']);
        }

        // Cook panel - للطباخين فقط
        if ($panel->getId() === 'cook') {
            return $this->type === 'cook';
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

    public function enrolledSections()
    {
        return $this->belongsToMany(LessonSection::class, 'lesson_section_student', 'student_id', 'lesson_section_id')
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

    // علاقات نظام المطبخ

    /**
     * المطابخ التي يعمل بها كطباخ
     */
    public function kitchens()
    {
        return $this->belongsToMany(Kitchen::class, 'kitchen_cooks', 'user_id', 'kitchen_id')
            ->withPivot('specialty', 'is_active')
            ->withTimestamps();
    }

    /**
     * سجلات العمل في المطابخ
     */
    public function kitchenCookRecords()
    {
        return $this->hasMany(KitchenCook::class);
    }

    /**
     * اشتراكات الطعام (كزبون)
     */
    public function kitchenSubscriptions()
    {
        return $this->hasMany(KitchenSubscription::class);
    }

    /**
     * الوجبات المستلمة (كزبون)
     */
    public function mealDeliveries()
    {
        return $this->hasMany(MealDelivery::class);
    }

    /**
     * الوجبات التي سلّمها (كطباخ)
     */
    public function deliveredMeals()
    {
        return $this->hasMany(MealDelivery::class, 'delivered_by');
    }

    /**
     * الفواتير (كزبون)
     */
    public function kitchenInvoices()
    {
        return $this->hasMany(KitchenInvoice::class);
    }

    /**
     * الفواتير المستلمة (كطباخ أو مدير)
     */
    public function collectedInvoices()
    {
        return $this->hasMany(KitchenInvoice::class, 'collected_by');
    }

    /**
     * مجموعات الزبائن
     */
    public function customerGroups()
    {
        return $this->belongsToMany(CustomerGroup::class, 'customer_group_members', 'user_id', 'group_id')
            ->withTimestamps();
    }

    /**
     * المصروفات التي أنشأها
     */
    public function createdExpenses()
    {
        return $this->hasMany(KitchenExpense::class, 'created_by');
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

    public function isCustomer()
    {
        return $this->type === 'customer';
    }

    public function isCook()
    {
        return $this->type === 'cook';
    }

    /**
     * هل لديه اشتراك طعام نشط؟
     */
    public function hasActiveKitchenSubscription(): bool
    {
        return $this->kitchenSubscriptions()->where('status', 'active')->exists();
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
