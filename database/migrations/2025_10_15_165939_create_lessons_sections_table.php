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
        Schema::create('lessons_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم القسم
            $table->text('description')->nullable(); // وصف القسم
            $table->boolean('is_active')->default(true); // حالة القسم (نشط/غير نشط)
            $table->string('color')->nullable(); // لون القسم للتمييز في الواجهة
            $table->integer('sort_order')->default(0); // ترتيب عرض الأقسام
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons_sections');
    }
};
