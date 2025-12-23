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
        Schema::create('kitchens', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المطبخ
            $table->text('description')->nullable(); // وصف المطبخ
            $table->string('location')->nullable(); // موقع المطبخ
            $table->string('phone', 20)->nullable(); // رقم الهاتف
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->timestamps();

            // Indexes
            $table->index('is_active');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchens');
    }
};
