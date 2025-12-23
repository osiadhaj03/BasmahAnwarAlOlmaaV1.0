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
        Schema::create('kitchen_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kitchen_id')->constrained('kitchens')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // الشخص الذي أنشأ المصروف
            $table->string('title'); // عنوان المصروف
            $table->text('description')->nullable(); // وصف المصروف
            $table->decimal('amount', 10, 2); // المبلغ
            $table->date('expense_date'); // تاريخ المصروف
            $table->timestamps();

            // Indexes
            $table->index('kitchen_id');
            $table->index('expense_date');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_expenses');
    }
};
