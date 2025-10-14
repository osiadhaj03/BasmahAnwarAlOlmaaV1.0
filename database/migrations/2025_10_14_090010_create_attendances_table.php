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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade'); // معرف الدرس
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade'); // معرف الطالب
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present'); // حالة الحضور
            $table->timestamp('attendance_date')->useCurrent(); // تاريخ ووقت تسجيل الحضور
            $table->string('used_code', 10)->nullable(); // الكود المستخدم في التسجيل
            $table->enum('attendance_method', ['code', 'manual', 'auto'])->default('code'); // طريقة تسجيل الحضور
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamp('marked_at')->nullable(); // وقت تعديل الحضور يدوياً
            $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null'); // من قام بتعديل الحضور
            $table->timestamps();

            // منع تسجيل الحضور أكثر من مرة لنفس الطالب في نفس الدرس
            $table->unique(['lesson_id', 'student_id']);
            
            // إضافة فهارس لتحسين الأداء
            $table->index(['lesson_id', 'attendance_date']);
            $table->index('student_id');
            $table->index('status');
            $table->index('attendance_date');
            $table->index('used_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
