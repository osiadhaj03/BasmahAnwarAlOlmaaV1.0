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
        Schema::table('attendance_codes', function (Blueprint $table) {
            // إضافة حقل للتحديث التلقائي
            $table->boolean('auto_refresh')->default(true)->after('is_active');
            
            // إضافة حقل فترة التحديث بالثواني (افتراضي 30 ثانية)
            $table->integer('refresh_interval')->default(30)->after('auto_refresh');
            
            // إضافة حقل وقت بدء العرض
            $table->timestamp('display_started_at')->nullable()->after('refresh_interval');
            
            // إضافة حقل آخر تحديث للكود
            $table->timestamp('last_refreshed_at')->nullable()->after('display_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_codes', function (Blueprint $table) {
            $table->dropColumn([
                'auto_refresh',
                'refresh_interval', 
                'display_started_at',
                'last_refreshed_at'
            ]);
        });
    }
};
