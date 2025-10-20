<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // الأدمن والمعلم يستطيعان رؤية المستخدمين
        return $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // الأدمن يرى كل المستخدمين، المعلم يرى طلاب دروسه فقط
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isTeacher()) {
            // المعلم يستطيع رؤية الطلاب المسجلين في دروسه فقط
            if ($model->isStudent()) {
                return $user->teacherLessons()
                    ->whereHas('students', function ($query) use ($model) {
                        $query->where('lesson_student.student_id', $model->id);
                    })->exists();
            }
            // المعلم يستطيع رؤية بياناته الشخصية
            return $user->id === $model->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // فقط الأدمن يستطيع إنشاء مستخدمين جدد
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // الأدمن يستطيع تعديل كل المستخدمين، المعلم يستطيع تعديل بياناته الشخصية فقط
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isTeacher()) {
            return $user->id === $model->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // فقط الأدمن يستطيع حذف المستخدمين
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
