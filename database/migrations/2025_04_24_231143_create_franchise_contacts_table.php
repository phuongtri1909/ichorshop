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
        Schema::create('franchise_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('franchise_code')->references('code')->on('franchises');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('provinces_code')->references('code')->on('provinces');
            $table->string('provinces_name')->nullable();
            $table->string('districts_code')->references('code')->on('districts');
            $table->string('districts_name')->nullable();
            $table->string('wards_code')->references('code')->on('wards');
            $table->string('wards_name')->nullable();
            $table->string('note')->nullable();
            $table->enum('status', ['pending', 'contacted', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchise_contacts');
    }
};
