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
        Schema::table('lessons', function (Blueprint $table) {
            // حذف القيد الخارجي أولاً
            $table->dropForeign(['teacher_id']);
            
            // إزالة الفهارس القديمة أولاً
            $table->dropIndex(['teacher_id', 'lesson_date']);
            $table->dropIndex(['lesson_date']);
            
            // إزالة الحقول القديمة
            $table->dropColumn('lesson_date');
            $table->dropColumn('location');
            
            // إضافة الحقول الجديدة
            $table->date('start_date')->after('teacher_id')->comment('تاريخ بداية الدرس');
            $table->date('end_date')->after('start_date')->comment('تاريخ نهاية الدرس');
            $table->json('lesson_days')->after('end_date')->comment('أيام الأسبوع للدرس');
            $table->enum('location_type', ['online', 'offline'])->after('end_time')->comment('نوع المكان');
            $table->string('location_details')->nullable()->after('location_type')->comment('تفاصيل المكان');
            $table->string('meeting_link')->nullable()->after('location_details')->comment('رابط الاجتماع للدروس الأونلاين');
            $table->boolean('is_recurring')->default(true)->after('meeting_link')->comment('هل الدرس متكرر');
            
            // إعادة إنشاء القيد الخارجي
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            
            // إضافة فهارس جديدة لتحسين الأداء
            $table->index(['teacher_id', 'start_date']);
            $table->index(['start_date', 'end_date']);
            $table->index('location_type');
            $table->index('is_recurring');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // حذف القيد الخارجي
            $table->dropForeign(['teacher_id']);
            
            // حذف الفهارس الجديدة
            $table->dropIndex(['teacher_id', 'start_date']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
            $table->dropIndex(['location_type']);
            $table->dropIndex(['is_recurring']);
            
            // حذف الأعمدة الجديدة
            $table->dropColumn([
                'start_date', 
                'end_date', 
                'lesson_days', 
                'location_type', 
                'location_details', 
                'meeting_link', 
                'is_recurring'
            ]);
            
            // إعادة إضافة الأعمدة القديمة
            $table->date('lesson_date')->after('lesson_section_id');
            $table->string('location')->nullable()->after('end_time');
            
            // إعادة إنشاء القيد الخارجي
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            
            // إعادة إضافة الفهارس القديمة
            $table->index(['teacher_id', 'lesson_date']);
            $table->index('lesson_date');
        });
    }
};
