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
        Schema::table('lessons_sections', function (Blueprint $table) {
            $table->boolean('is_mandatory')->default(true)->after('is_active')->comment('هل الدورة إجبارية (تحسب الحضور والغياب)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons_sections', function (Blueprint $table) {
            $table->dropColumn('is_mandatory');
        });
    }
};
