<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('invoices', 'pearl_city_fee_amount')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->decimal('pearl_city_fee_amount', 60, 2)->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('invoices', 'pearl_city_fee_amount')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('pearl_city_fee_amount');
            });
        }
    }
};
