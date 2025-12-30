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
        // إضافة is_mandatory لجدول lessons
        Schema::table('lessons', function (Blueprint $table) {
            $table->boolean('is_mandatory')->default(true)->after('status')->comment('هل الدورة إجبارية (تحسب الحضور والغياب)');
        });

        // حذف is_mandatory من جدول lessons_sections
        Schema::table('lessons_sections', function (Blueprint $table) {
            $table->dropColumn('is_mandatory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // استرجاع is_mandatory لجدول lessons_sections
        Schema::table('lessons_sections', function (Blueprint $table) {
            $table->boolean('is_mandatory')->default(true)->after('is_active');
        });

        // حذف is_mandatory من جدول lessons
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('is_mandatory');
        });
    }
};
