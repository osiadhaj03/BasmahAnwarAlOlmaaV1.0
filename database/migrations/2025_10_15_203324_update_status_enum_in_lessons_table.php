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
            // Drop the existing enum column and recreate it with new values
            $table->dropColumn('status');
        });
        
        Schema::table('lessons', function (Blueprint $table) {
            // Recreate the column with all required enum values
            $table->enum('status', [
                'scheduled',     // مجدول
                'in_progress',   // جاري
                'active',        // نشط (للتوافق مع القيم القديمة)
                'completed',     // مكتمل
                'cancelled'      // ملغي
            ])->default('scheduled')->after('max_students');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Drop the updated column
            $table->dropColumn('status');
        });
        
        Schema::table('lessons', function (Blueprint $table) {
            // Restore the original enum values
            $table->enum('status', [
                'active', 
                'cancelled', 
                'completed'
            ])->default('active')->after('max_students');
        });
    }
};
