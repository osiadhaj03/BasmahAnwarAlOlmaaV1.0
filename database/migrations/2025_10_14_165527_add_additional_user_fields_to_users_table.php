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
            // التحقق من وجود الأعمدة قبل إضافتها
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id', 50)->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'academic_level')) {
                $table->enum('academic_level', ['elementary', 'middle', 'high', 'university'])->nullable();
            }
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id', 50)->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'specialization')) {
                $table->enum('specialization', [
                    'arabic', 'english', 'math', 'science', 'physics', 'chemistry', 
                    'biology', 'history', 'geography', 'islamic', 'computer', 
                    'art', 'sports', 'music', 'other'
                ])->nullable();
            }
            if (!Schema::hasColumn('users', 'hire_date')) {
                $table->date('hire_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable();
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'student_id',
                'academic_level',
                'employee_id',
                'department',
                'specialization',
                'hire_date',
                'bio',
                'address'
            ]);
        });
    }
};
