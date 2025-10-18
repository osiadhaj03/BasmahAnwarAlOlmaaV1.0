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
        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان المحاضرة
            $table->text('description')->nullable(); // وصف المحاضرة
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade'); // ربط بالدورة
            $table->integer('lecture_number')->default(1); // رقم المحاضرة في الدورة
            $table->dateTime('lecture_date')->nullable(); // تاريخ ووقت المحاضرة
            $table->integer('duration_minutes')->default(60); // مدة المحاضرة بالدقائق
            $table->string('location')->nullable(); // مكان المحاضرة
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled'); // حالة المحاضرة
            $table->text('notes')->nullable(); // ملاحظات المحاضرة
            $table->string('recording_url')->nullable(); // رابط تسجيل المحاضرة
            $table->json('materials')->nullable(); // مواد المحاضرة (ملفات، روابط، إلخ)
            $table->boolean('is_mandatory')->default(true); // هل المحاضرة إجبارية
            $table->timestamps();
            
            // إضافة فهرس للبحث السريع
            $table->index(['lesson_id', 'lecture_number']);
            $table->index(['lesson_id', 'lecture_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lectures');
    }
};
