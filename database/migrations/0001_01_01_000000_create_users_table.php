<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المستخدم
            $table->string('email')->unique(); // البريد الإلكتروني
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('type', ['admin', 'teacher', 'student'])->default('student'); // نوع المستخدم
            $table->string('phone', 20)->nullable(); // رقم الهاتف
            $table->string('student_id', 50)->nullable()->unique(); // الرقم الجامعي للطالب
            $table->string('employee_id', 50)->nullable()->unique(); // الرقم الوظيفي للموظف
            $table->text('bio')->nullable(); // نبذة شخصية
            $table->string('department', 100)->nullable(); // القسم أو الكلية
            $table->date('birth_date')->nullable(); // تاريخ الميلاد
            $table->enum('gender', ['male', 'female'])->nullable(); // الجنس
            $table->string('address')->nullable(); // العنوان
            $table->boolean('is_active')->default(true); // هل المستخدم نشط
            $table->timestamp('last_login_at')->nullable(); // آخر تسجيل دخول
            $table->rememberToken();
            $table->timestamps();

            // إضافة فهارس لتحسين الأداء
            $table->index('type');
            $table->index('student_id');
            $table->index('employee_id');
            $table->index('is_active');
            $table->index(['type', 'is_active']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
