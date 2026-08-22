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
        Schema::table('product_price_histories', function (Blueprint $table) {
            $table->decimal('old_stock', 12, 2)->nullable()->after('user_id');
            $table->decimal('new_stock', 12, 2)->nullable()->after('old_stock');
            $table->string('reason', 500)->nullable()->after('new_cost_price');
        });
    }

    public function down(): void
    {
        Schema::table('product_price_histories', function (Blueprint $table) {
            $table->dropColumn(['old_stock', 'new_stock', 'reason']);
        });
    }
};
