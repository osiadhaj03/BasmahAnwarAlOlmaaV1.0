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
        Schema::table('users', function (Blueprint $table) {
            // Drop the existing enum column and recreate it with new values
            $table->dropColumn('academic_level');
        });
        
        Schema::table('users', function (Blueprint $table) {
            // Recreate the column with all required enum values
            $table->enum('academic_level', [
                'elementary', 
                'middle', 
                'high', 
                'university',
                'bachelor',
                'master',
                'doctorate',
                'intermediate_diploma',
                'higher_diploma',
                'other'
            ])->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the updated column
            $table->dropColumn('academic_level');
        });
        
        Schema::table('users', function (Blueprint $table) {
            // Restore the original enum values
            $table->enum('academic_level', [
                'elementary', 
                'middle', 
                'high', 
                'university'
            ])->nullable()->after('phone');
        });
    }
};
