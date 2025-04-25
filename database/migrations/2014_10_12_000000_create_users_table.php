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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable(); 
            $table->string('email')->unique()->nullable();
            $table->string('image_profile')->nullable(); 
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); 
            $table->string('tempat_lahir')->nullable(); 
            $table->date('tanggal_lahir')->nullable(); 
            $table->string('gender')->nullable(); 
            $table->string('lokasi')->nullable(); 
            $table->string('nomor_telepon')->nullable(); 
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
