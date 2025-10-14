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
        Schema::create('attendance_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade'); // معرف الدرس
            $table->string('code', 10)->unique(); // الكود العشوائي (6-10 أرقام)
            $table->timestamp('created_at')->useCurrent(); // وقت إنشاء الكود
            $table->timestamp('expires_at'); // وقت انتهاء صلاحية الكود
            $table->boolean('is_active')->default(true); // هل الكود نشط أم لا
            $table->integer('usage_count')->default(0); // عدد مرات استخدام الكود
            $table->integer('max_usage')->nullable(); // الحد الأقصى لاستخدام الكود
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // من أنشأ الكود (الأستاذ)
            $table->timestamp('deactivated_at')->nullable(); // وقت إلغاء تفعيل الكود
            $table->foreignId('deactivated_by')->nullable()->constrained('users')->onDelete('set null'); // من ألغى تفعيل الكود
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamp('updated_at')->nullable();

            // إضافة فهارس لتحسين الأداء
            $table->index('lesson_id');
            $table->index('code');
            $table->index('expires_at');
            $table->index('is_active');
            $table->index(['lesson_id', 'is_active']);
            $table->index(['code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_codes');
    }
};
