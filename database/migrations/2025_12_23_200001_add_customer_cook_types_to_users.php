<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum type to include 'customer' and 'cook'
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('admin', 'teacher', 'student', 'customer', 'cook') DEFAULT 'student'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('admin', 'teacher', 'student') DEFAULT 'student'");
    }
};
