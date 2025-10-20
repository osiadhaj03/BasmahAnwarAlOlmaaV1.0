<?php

namespace App\Policies;

use App\Models\Lecture;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LecturePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // الأدمن والمعلم يستطيعان رؤية المحاضرات
        return $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lecture $lecture): bool
    {
        // الأدمن يرى كل المحاضرات، المعلم يرى محاضرات دروسه فقط
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isTeacher()) {
            return $lecture->lesson->teacher_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // الأدمن والمعلم يستطيعان إنشاء محاضرات
        return $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lecture $lecture): bool
    {
        // الأدمن يستطيع تعديل كل المحاضرات، المعلم يستطيع تعديل محاضرات دروسه فقط
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isTeacher()) {
            return $lecture->lesson->teacher_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lecture $lecture): bool
    {
        // الأدمن يستطيع حذف كل المحاضرات، المعلم يستطيع حذف محاضرات دروسه فقط
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isTeacher()) {
            return $lecture->lesson->teacher_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lecture $lecture): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lecture $lecture): bool
    {
        return false;
    }
}
