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
        Schema::table('attendances', function (Blueprint $table) {
            // إزالة الفهارس والقيود الحالية
            $table->dropForeign(['lesson_id']);
            $table->dropUnique(['lesson_id', 'student_id']);
            $table->dropIndex(['lesson_id', 'attendance_date']);
            
            // إضافة العمود الجديد lecture_id
            $table->foreignId('lecture_id')->nullable()->after('id')->constrained('lectures')->onDelete('cascade');
            
            // إضافة الفهارس الجديدة
            $table->unique(['lecture_id', 'student_id']);
            $table->index(['lecture_id', 'attendance_date']);
        });
        
        // في خطوة منفصلة، نقوم بحذف العمود القديم بعد نقل البيانات
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('lesson_id');
        });
        
        // جعل lecture_id غير nullable
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('lecture_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // إعادة إضافة lesson_id
            $table->foreignId('lesson_id')->nullable()->after('id')->constrained('lessons')->onDelete('cascade');
            
            // إزالة القيود والفهارس الجديدة
            $table->dropForeign(['lecture_id']);
            $table->dropUnique(['lecture_id', 'student_id']);
            $table->dropIndex(['lecture_id', 'attendance_date']);
            
            // حذف العمود الجديد
            $table->dropColumn('lecture_id');
            
            // إعادة إضافة الفهارس القديمة
            $table->unique(['lesson_id', 'student_id']);
            $table->index(['lesson_id', 'attendance_date']);
            
            // جعل lesson_id غير nullable
            $table->foreignId('lesson_id')->nullable(false)->change();
        });
    }
};
