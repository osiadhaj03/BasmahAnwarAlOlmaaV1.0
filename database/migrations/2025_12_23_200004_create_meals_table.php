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
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kitchen_id')->constrained('kitchens')->onDelete('cascade');
            $table->string('name'); // اسم الوجبة
            $table->text('description')->nullable(); // وصف الوجبة
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner']); // نوع الوجبة
            $table->string('image')->nullable(); // صورة الوجبة
            $table->timestamps();

            // Indexes
            $table->index('meal_type');
            $table->index('kitchen_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
