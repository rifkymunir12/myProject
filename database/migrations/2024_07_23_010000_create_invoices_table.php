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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->nullable();
            $table->uuid( 'shipment_id')->nullable();
            $table->uuid('coupon_id')->nullable();
            $table->string('place', 256)->nullable();
            $table->float('total_price')->nullable();
            $table->float('final_price')->nullable();
            //$table->float('payment')->nullable();
            $table->string('note', 128)->nullable();
           
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('shipment_id')
                ->references('id')
                ->on('shipments')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('coupon_id')
                ->references('id')
                ->on('coupons')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
