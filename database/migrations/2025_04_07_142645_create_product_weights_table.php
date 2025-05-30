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
        Schema::create('product_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique(); // Mã SKU
            $table->string('weight');
            $table->decimal('original_price', 10, 0); // Giá gốc
            $table->decimal('discount_percent', 5, 2)->default(0); // Phần trăm giảm giá
            $table->decimal('discounted_price', 10, 0); // Giá sau khi giảm
            $table->boolean('is_default')->default(false); // Tùy chọn mặc định
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['product_id', 'weight']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_weights');
    }
};