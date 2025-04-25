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
        Schema::table('items', function (Blueprint $table) {
            $table->double('price',12, 2)->nullable()->change();
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->double('price',12, 2)->nullable()->change();
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->double('total_price', 12, 2)->nullable()->change();
            $table->double('final_price', 12, 2)->nullable()->change();
            //$table->double('payment', 12, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
