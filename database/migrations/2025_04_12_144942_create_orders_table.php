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
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('product_name')->nullable();
            $table->foreignId('product_weight_id')->nullable()->constrained('product_weights')->onDelete('set null');
            $table->string('discounted_price')->nullable();
            $table->string('product_weight_sku')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', [
                'pending',      // Chờ xác nhận
                'processing',   // Đang xử lý
                'shipping',     // Đang giao hàng
                'completed',    // Hoàn thành
                'cancelled'     // Đã hủy
            ])->default('pending');
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
