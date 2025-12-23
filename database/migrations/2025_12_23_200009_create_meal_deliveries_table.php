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
        Schema::create('meal_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // الزبون
            $table->foreignId('meal_id')->nullable()->constrained('meals')->onDelete('set null'); // الوجبة
            $table->foreignId('delivered_by')->nullable()->constrained('users')->onDelete('set null'); // الطباخ المسلّم
            $table->foreignId('subscription_id')->constrained('kitchen_subscriptions')->onDelete('cascade'); // الاشتراك
            $table->date('delivery_date'); // تاريخ التسليم
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner']); // نوع الوجبة
            $table->enum('status', ['pending', 'delivered', 'missed'])->default('pending'); // الحالة
            $table->timestamp('delivered_at')->nullable(); // وقت التسليم الفعلي
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('delivery_date');
            $table->index('status');
            $table->index(['subscription_id', 'delivery_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_deliveries');
    }
};
