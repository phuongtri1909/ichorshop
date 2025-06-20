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
        Schema::create('paypal_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('mode', ['sandbox', 'live'])->default('sandbox');

            // Sandbox credentials
            $table->string('sandbox_username')->nullable();
            $table->text('sandbox_password')->nullable();
            $table->text('sandbox_secret')->nullable();
            $table->text('sandbox_certificate')->nullable();
            $table->string('sandbox_app_id')->nullable()->default('APP-80W284485P519543T');

            // Live credentials
            $table->string('live_username')->nullable();
            $table->text('live_password')->nullable();
            $table->text('live_secret')->nullable();
            $table->text('live_certificate')->nullable();
            $table->string('live_app_id')->nullable();

            // Common config
            $table->string('payment_action')->default('Sale');
            $table->string('currency')->default('USD');
            $table->string('billing_type')->default('MerchantInitiatedBilling');
            $table->string('notify_url')->nullable();
            $table->string('locale')->nullable();
            $table->boolean('validate_ssl')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paypal_settings');
    }
};
