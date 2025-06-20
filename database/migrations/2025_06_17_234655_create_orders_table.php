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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
            $table->string('order_code')->unique();
            $table->string('phone');
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('address');
            $table->string('postal_code')->nullable();
            $table->string('apt')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method');
            $table->string('status_payment')->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('user_notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
