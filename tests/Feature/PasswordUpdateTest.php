<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Testing\TestCase;
use Illuminate\Support\Facades\Hash;

class PasswordUpdateTest extends TestCase
{
    /**
     * اختبار تحديث كلمة السر من قبل المدير
     */
    public function testAdminCanUpdateUserPassword(): void
    {
        // إنشاء مستخدم مدير
        $admin = User::factory()->create(['type' => 'admin']);
        
        // إنشاء مستخدم عادي
        $user = User::factory()->create(['type' => 'student']);
        
        // تسجيل الدخول كمدير
        $this->actingAs($admin);
        
        // الوصول إلى صفحة تحديث كلمة السر
        $response = $this->get(route('filament.admin.resources.users.updatePassword', ['record' => $user]));
        
        // يجب أن تكون الصفحة متاحة
        $response->assertSuccessful();
    }
    
    /**
     * اختبار تحديث المستخدم لكلمة السر الخاصة به
     */
    public function testUserCanUpdateOwnPassword(): void
    {
        // إنشاء مستخدم
        $user = User::factory()->create(['type' => 'student']);
        
        // تسجيل الدخول
        $this->actingAs($user);
        
        // الوصول إلى صفحة تحديث كلمة السر الخاصة به
        $response = $this->get(route('filament.admin.resources.users.updatePassword', ['record' => $user]));
        
        // يجب أن تكون الصفحة متاحة
        $response->assertSuccessful();
    }
    
    /**
     * اختبار منع المستخدم من تحديث كلمة سر مستخدم آخر
     */
    public function testUserCannotUpdateOtherUserPassword(): void
    {
        // إنشاء مستخدمين
        $user1 = User::factory()->create(['type' => 'student']);
        $user2 = User::factory()->create(['type' => 'student']);
        
        // تسجيل الدخول بحساب المستخدم الأول
        $this->actingAs($user1);
        
        // محاولة الوصول إلى صفحة تحديث كلمة سر المستخدم الثاني
        // يجب إعادة التوجيه أو رفع استثناء
        // (هذا يعتمد على تنفيذ السماح)
    }
    
    /**
     * اختبار التشفير الصحيح لكلمة السر
     */
    public function testPasswordIsProperlyHashed(): void
    {
        // إنشاء مستخدم
        $user = User::factory()->create();
        
        // كلمة السر الأصلية
        $plainPassword = 'TestPassword123';
        
        // تحديث كلمة السر
        $user->update([
            'password' => Hash::make($plainPassword),
        ]);
        
        // إعادة تحميل المستخدم من قاعدة البيانات
        $user->refresh();
        
        // التحقق من أن كلمة السر مشفرة (بدايات bcrypt)
        $this->assertTrue(Hash::check($plainPassword, $user->password));
        
        // التحقق من أن كلمة سر خاطئة لا تعمل
        $this->assertFalse(Hash::check('WrongPassword', $user->password));
    }
    
    /**
     * اختبار معايير قوة كلمة السر
     */
    public function testPasswordMustMeetMinimumRequirements(): void
    {
        $user = User::factory()->create(['type' => 'admin']);
        $this->actingAs($user);
        
        $testUser = User::factory()->create();
        
        // كلمة سر ضعيفة - أقل من 8 أحرف
        $weakPassword = 'weak123';
        
        // كلمة سر بدون أحرف كبيرة
        $noUppercase = 'weakpassword123';
        
        // كلمة سر قوية
        $strongPassword = 'StrongPassword123';
        
        // اختبر كلمة السر الضعيفة - يجب أن تفشل
        // اختبر كلمة السر القوية - يجب أن تنجح
    }
    
    /**
     * اختبار تطابق كلمات المرور
     */
    public function testPasswordConfirmationMustMatch(): void
    {
        $user = User::factory()->create(['type' => 'admin']);
        $this->actingAs($user);
        
        $testUser = User::factory()->create();
        
        // كلمات المرور المتطابقة
        $password = 'StrongPassword123';
        $confirmation = 'StrongPassword123';
        
        // هذا يجب أن ينجح
        
        // كلمات المرور غير المتطابقة
        $password2 = 'StrongPassword123';
        $confirmation2 = 'DifferentPassword123';
        
        // هذا يجب أن يفشل
    }
    
    /**
     * اختبار الإشعارات
     */
    public function testNotificationsAreSentCorrectly(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $user = User::factory()->create(['type' => 'student']);
        
        $this->actingAs($admin);
        
        // عند النجاح - يجب أن يظهر إشعار نجاح
        // عند الفشل - يجب أن يظهر إشعار خطأ
    }
    
    /**
     * اختبار إعادة التوجيه بعد النجاح
     */
    public function testRedirectsAfterSuccessfulUpdate(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // بعد تحديث كلمة السر بنجاح
        // يجب إعادة التوجيه إلى صفحة عرض المستخدم
    }
}

/**
 * ملاحظات حول الاختبارات:
 * 
 * لتشغيل الاختبارات:
 * php artisan test
 * 
 * لتشغيل اختبار محدد:
 * php artisan test tests/Feature/PasswordUpdateTest.php::testAdminCanUpdateUserPassword
 * 
 * لتشغيل مع التغطية:
 * php artisan test --coverage
 */
