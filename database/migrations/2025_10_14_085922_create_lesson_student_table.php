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
        Schema::create('lesson_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade'); // معرف الدرس
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade'); // معرف الطالب
            $table->timestamp('enrolled_at')->useCurrent(); // تاريخ التسجيل في الدرس
            $table->enum('enrollment_status', ['active', 'dropped', 'completed'])->default('active'); // حالة التسجيل
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamps();

            // منع تسجيل الطالب في نفس الدرس أكثر من مرة
            $table->unique(['lesson_id', 'student_id']);
            
            // إضافة فهارس لتحسين الأداء
            $table->index('lesson_id');
            $table->index('student_id');
            $table->index('enrollment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_student');
    }
};
