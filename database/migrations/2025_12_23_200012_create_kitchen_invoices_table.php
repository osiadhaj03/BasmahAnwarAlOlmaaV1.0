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
        Schema::create('kitchen_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('kitchen_subscriptions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // الزبون
            $table->string('invoice_number')->unique(); // رقم الفاتورة
            $table->decimal('amount', 10, 2); // المبلغ
            $table->date('billing_date'); // تاريخ الفاتورة
            $table->date('due_date'); // تاريخ الاستحقاق
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending'); // الحالة
            $table->foreignId('collected_by')->nullable()->constrained('users')->onDelete('set null'); // المستلم (طباخ أو مدير)
            $table->string('received_from')->nullable(); // اسم المستلم منه (يدوي)
            $table->timestamp('paid_at')->nullable(); // تاريخ الدفع
            $table->enum('payment_method', ['cash', 'bank_transfer'])->nullable(); // طريقة الدفع
            $table->timestamps();

            // Indexes
            $table->index('subscription_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('billing_date');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_invoices');
    }
};
