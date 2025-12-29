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
        // تغيير عمود status ليقبل قيمة 'partial'
        DB::statement("ALTER TABLE kitchen_invoices MODIFY COLUMN status ENUM('pending', 'paid', 'partial', 'overdue', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إرجاع العمود للحالة السابقة
        DB::statement("ALTER TABLE kitchen_invoices MODIFY COLUMN status ENUM('pending', 'paid', 'overdue', 'cancelled') DEFAULT 'pending'");
    }
};
