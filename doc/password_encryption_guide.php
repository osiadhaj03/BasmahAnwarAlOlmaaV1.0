<?php

/**
 * شرح تفصيلي لنظام تشفير كلمات السر
 * Password Encryption System Documentation
 * 
 * هذا الملف يوضح كيفية عمل نظام التشفير في المشروع
 */

// ============================================
// 1. كيفية تشفير كلمة السر عند الإنشاء
// ============================================

/**
 * عند إنشاء مستخدم جديد في UserForm.php:
 * 
 * TextInput::make('password')
 *     ->label('كلمة المرور')
 *     ->password()
 *     ->required(fn (string $context): bool => $context === 'create')
 *     ->minLength(8)
 *     ->same('passwordConfirmation')
 *     ->dehydrated(fn ($state): bool => filled($state)),
 * 
 * ثم في CreateUser.php يتم التشفير تلقائياً بسبب:
 * protected $casts = [
 *     'password' => 'hashed',
 * ];
 * 
 * هذا يعني أن Laravel يشفر كلمة المرور تلقائياً قبل الحفظ
 */

// ============================================
// 2. كيفية تشفير كلمة السر في UpdatePassword
// ============================================

/**
 * في UpdatePassword.php:
 * 
 * $this->record->update([
 *     'password' => Hash::make($data['password']),
 * ]);
 * 
 * - Hash::make() تستخدم bcrypt (الخوارزمية الافتراضية في Laravel)
 * - bcrypt هي خوارزمية تجزئة آمنة وبطيئة بقصد (Slow Hashing)
 * - كل مرة تُشفّر نفس كلمة المرور بطريقة مختلفة (بسبب الملح العشوائي)
 */

// ============================================
// 3. التحقق من كلمة المرور
// ============================================

/**
 * عند التحقق من تسجيل الدخول، Laravel يفعل:
 * 
 * if (Hash::check($password, $user->password)) {
 *     // كلمة المرور صحيحة
 * }
 * 
 * - Hash::check() تقارن كلمة المرور العادية مع النسخة المشفرة
 * - لا تستطيع الحصول على النص الأصلي من النسخة المشفرة (اتجاه واحد)
 */

// ============================================
// 4. معايير قوة كلمة السر في PasswordUpdateForm
// ============================================

/**
 * التحقق من قوة كلمة السر:
 * 
 * 1. الحد الأدنى: 8 أحرف
 *    ->minLength(8)
 * 
 * 2. تطابق كلمات المرور:
 *    ->same('passwordConfirmation')
 * 
 * 3. نمط Regex:
 *    ->rule('regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/')
 *    
 *    هذا النمط يتحقق من:
 *    - (?=.*[a-z]) - وجود أحرف صغيرة
 *    - (?=.*[A-Z]) - وجود أحرف كبيرة
 *    - (?=.*\d) - وجود أرقام
 */

// ============================================
// 5. مثال عملي للتشفير والتحقق
// ============================================

namespace App\Examples;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PasswordEncryptionExample
{
    /**
     * مثال: تشفير كلمة السر الجديدة
     */
    public static function encryptPasswordExample()
    {
        // كلمة السر الأصلية
        $plainPassword = "MyPassword123";
        
        // تشفير باستخدام Hash::make
        $hashedPassword = Hash::make($plainPassword);
        
        // سيبدو مثل:
        // $2y$10$wj5ZrT9Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5
        
        return [
            'plain' => $plainPassword,
            'hashed' => $hashedPassword,
        ];
    }
    
    /**
     * مثال: التحقق من كلمة السر
     */
    public static function verifyPasswordExample()
    {
        $user = User::find(1);
        $plaintextPassword = "MyPassword123";
        
        // التحقق من كلمة المرور
        if (Hash::check($plaintextPassword, $user->password)) {
            return "كلمة المرور صحيحة!";
        } else {
            return "كلمة المرور غير صحيحة!";
        }
    }
    
    /**
     * مثال: تحديث كلمة السر
     */
    public static function updatePasswordExample()
    {
        $user = User::find(1);
        $newPassword = "NewPassword123";
        
        // طريقة 1: باستخدام Hash::make (مثل UpdatePassword page)
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
        
        // طريقة 2: باستخدام الـ cast (تلقائي)
        // لكن بما أن PasswordUpdateForm تستخدم Hash::make بشكل صريح
        // فإننا نستخدم الطريقة 1
        
        return "تم تحديث كلمة المرور بنجاح";
    }
}

// ============================================
// 6. مقارنة بين طرق التشفير المختلفة
// ============================================

/**
 * الطرق المستخدمة في المشروع:
 * 
 * 1. عند الإنشاء (CreateUser.php):
 *    - استخدام Cast 'hashed' في User Model
 *    - Laravel يشفر تلقائياً قبل الحفظ
 *    - مثالي للإنشاء والتحديثات البسيطة
 * 
 * 2. عند التحديث المخصص (UpdatePassword.php):
 *    - استخدام Hash::make() بشكل صريح
 *    - نحصل على تحكم أكثر
 *    - يمكن إضافة تحقق إضافي قبل التحديث
 *    - مثالي لعمليات معقدة
 * 
 * كلا الطريقتين تستخدم نفس خوارزمية التشفير (bcrypt)
 * وبالتالي النتيجة متوافقة 100%
 */

// ============================================
// 7. خصائص أمان bcrypt
// ============================================

/**
 * لماذا bcrypt آمن؟
 * 
 * 1. بطيء بقصد (Intentionally Slow)
 *    - يأخذ وقت لحساب التجزئة (حوالي 100ms)
 *    - يجعل هجمات القوة الغاشمة بطيئة جداً
 * 
 * 2. ملح عشوائي (Salt)
 *    - كل تشفير يضيف ملح عشوائي
 *    - نفس كلمة السر تنتج تشفير مختلف في كل مرة
 *    - يمنع هجمات جداول البحث (Rainbow Tables)
 * 
 * 3. عامل العمل (Work Factor)
 *    - يمكن زيادة تعقيد التشفير مع الوقت
 *    - عندما تصبح أجهزة الكمبيوتر أسرع، يزداد العامل
 *    - ضمان الأمان في المستقبل
 * 
 * 4. غير قابل للعكس (One-Way)
 *    - لا يمكن الحصول على النص الأصلي من التجزئة
 *    - يمكن فقط مقارنة التجزئ الجديدة مع المحفوظة
 */

// ============================================
// 8. ملفات معنية بالتشفير
// ============================================

/**
 * ملفات المشروع المتعلقة بكلمات السر:
 * 
 * - app/Models/User.php
 *   protected $casts = ['password' => 'hashed'];
 * 
 * - app/Filament/Resources/Users/Schemas/UserForm.php
 *   يعرض نموذج تعديل كلمة السر عند الإنشاء
 * 
 * - app/Filament/Resources/Users/Schemas/PasswordUpdateForm.php (جديد)
 *   نموذج متخصص لتحديث كلمة السر
 * 
 * - app/Filament/Resources/Users/Pages/UpdatePassword.php (جديد)
 *   صفحة متخصصة لتحديث كلمة السر
 * 
 * - resources/views/filament/resources/users/pages/update-password.blade.php (جديد)
 *   واجهة المستخدم لتحديث كلمة السر
 * 
 * - config/hashing.php
 *   إعدادات التشفير (افتراضي: bcrypt)
 */

// ============================================
// 9. اختبار التشفير
// ============================================

/**
 * للاختبار في Tinker:
 * 
 * php artisan tinker
 * 
 * // 1. إنشاء مستخدم بكلمة سر جديدة
 * $user = User::find(1);
 * $user->password = bcrypt('TestPassword123');
 * $user->save();
 * 
 * // 2. التحقق من التشفير
 * echo $user->password; // يظهر التجزئة المشفرة
 * 
 * // 3. التحقق من كلمة المرور
 * Hash::check('TestPassword123', $user->password); // true
 * Hash::check('WrongPassword', $user->password);   // false
 * 
 * // 4. تحديث كلمة السر
 * $user->update(['password' => Hash::make('NewPassword123')]);
 * 
 * // 5. التحقق من الكلمة الجديدة
 * Hash::check('NewPassword123', $user->fresh()->password); // true
 */

// ============================================
// 10. ملاحظات أمان مهمة
// ============================================

/**
 * ✓ يجب فعله:
 * - استخدام Hash::make() أو Cast 'hashed' دائماً
 * - استخدام Hash::check() للتحقق من كلمات السر
 * - لا تخزن كلمات السر بالنص الصريح أبداً
 * - فرض كلمات سر قوية (ما يفعله PasswordUpdateForm)
 * - استخدام HTTPS عند إرسال كلمات السر
 * - تسجيل الدخول بنجاح بعد تغيير كلمة السر
 * 
 * ✗ لا تفعل:
 * - حفظ كلمات السر بدون تشفير
 * - مشاركة كلمات السر عبر البريد
 * - استخدام تشفير ضعيف (md5, sha1)
 * - الاحتفاظ بكلمات السر في السجلات
 * - إرسال كلمات السر عبر HTTP غير آمن
 */

echo "تم تحضير ملف التوثيق بنجاح!";
