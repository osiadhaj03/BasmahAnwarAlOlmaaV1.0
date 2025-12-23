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
        Schema::create('kitchen_cooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kitchen_id')->constrained('kitchens')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // المستخدم من نوع cook
            $table->string('specialty')->nullable(); // التخصص
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->unique(['kitchen_id', 'user_id']); // طباخ واحد لا يمكن إضافته مرتين لنفس المطبخ
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_cooks');
    }
};
