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
        Schema::create('kitchen_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // الزبون
            $table->foreignId('kitchen_id')->constrained('kitchens')->onDelete('cascade');
            $table->date('start_date'); // تاريخ البداية
            $table->date('end_date')->nullable(); // تاريخ النهاية
            $table->enum('status', ['active', 'paused', 'cancelled'])->default('active'); // الحالة
            $table->decimal('monthly_price', 10, 2); // السعر الشهري
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('kitchen_id');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_subscriptions');
    }
};
