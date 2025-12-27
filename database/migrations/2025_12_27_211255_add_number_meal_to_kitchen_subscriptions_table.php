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
        Schema::table('kitchen_subscriptions', function (Blueprint $table) {
            $table->integer('number_meal')->default(1)->after('monthly_price'); // عدد الوجبات اليومية
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kitchen_subscriptions', function (Blueprint $table) {
            $table->dropColumn('number_meal');
        });
    }
};
