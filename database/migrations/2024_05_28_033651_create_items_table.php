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
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 128)->nullable();
            //$table->string('unit', 64)->nullable();
            $table->float('price', 11 , 2)->nullable();
            $table->string('description', 1024)->nullable();
            $table->float('amount', 12 , 2)->unsigned()->nullable();
            $table->float('stock_in', 12 , 2)->unsigned()->nullable();
            $table->float('stock_out', 12 , 2)->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
