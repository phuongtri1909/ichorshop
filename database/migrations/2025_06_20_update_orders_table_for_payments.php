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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_id')->nullable()->after('status_payment');
            $table->string('payer_id')->nullable()->after('payment_id');
            $table->string('payment_transaction_id')->nullable()->after('payer_id');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('payment_transaction_id');
            $table->string('refund_reason')->nullable()->after('refund_amount');
            $table->timestamp('refunded_at')->nullable()->after('refund_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_id',
                'payer_id',
                'payment_transaction_id',
                'refund_amount',
                'refund_reason',
                'refunded_at'
            ]);
        });
    }
};