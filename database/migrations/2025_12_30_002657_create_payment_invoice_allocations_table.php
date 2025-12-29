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
        Schema::create('payment_invoice_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('kitchen_payments')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('kitchen_invoices')->cascadeOnDelete();
            $table->decimal('amount_allocated', 10, 2);
            $table->timestamps();

            // Index for faster queries
            $table->index(['payment_id', 'invoice_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_invoice_allocations');
    }
};
