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
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('postal_code', 20)->nullable()->after('city_id');
            $table->string('label', 100)->nullable()->after('postal_code');
            $table->boolean('is_default')->default(false)->after('label');
            
            // Add index for faster queries
            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_default']);
            $table->dropColumn(['postal_code', 'label', 'is_default']);
        });
    }
};