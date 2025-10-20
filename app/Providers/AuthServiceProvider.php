<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Lecture;
use App\Models\Attendance;
use App\Policies\UserPolicy;
use App\Policies\LessonPolicy;
use App\Policies\LecturePolicy;
use App\Policies\AttendancePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Lesson::class => LessonPolicy::class,
        Lecture::class => LecturePolicy::class,
        Attendance::class => AttendancePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // إضافة Gates إضافية إذا لزم الأمر
        Gate::define('access-admin-panel', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('access-teacher-panel', function (User $user) {
            return $user->isTeacher();
        });

        Gate::define('access-student-panel', function (User $user) {
            return $user->isStudent();
        });
    }
}