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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان الدرس
            $table->text('description')->nullable(); // وصف الدرس
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade'); // معرف الأستاذ
            $table->date('lesson_date'); // تاريخ الدرس
            $table->time('start_time'); // وقت بداية الدرس
            $table->time('end_time'); // وقت نهاية الدرس
            $table->string('location')->nullable(); // مكان الدرس
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active'); // حالة الدرس
            $table->integer('max_students')->nullable(); // الحد الأقصى للطلاب
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamps();

            // إضافة فهارس لتحسين الأداء
            $table->index(['teacher_id', 'lesson_date']);
            $table->index('lesson_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
