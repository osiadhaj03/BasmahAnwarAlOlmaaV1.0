<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ترحيل أنواع المستخدمين القديمة إلى نظام الأدوار الجديد
     */
    public function up(): void
    {
        // الحصول على جميع المستخدمين
        $users = User::all();
        
        foreach ($users as $user) {
            // تخطي إذا لم يكن هناك نوع
            if (empty($user->type)) {
                continue;
            }
            
            // البحث عن الدور المطابق
            $role = Role::where('slug', $user->type)->first();
            
            if ($role) {
                // إضافة الدور للمستخدم إذا لم يكن موجوداً
                if (!$user->roles()->where('role_id', $role->id)->exists()) {
                    $user->roles()->attach($role->id);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف جميع علاقات role_user
        DB::table('role_user')->truncate();
    }
};
