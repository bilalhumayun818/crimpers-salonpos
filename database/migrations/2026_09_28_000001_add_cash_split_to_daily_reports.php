<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            // Cash payments (new dedicated section replacing free-text balance_notes)
            $table->json('cash_entries')->nullable()->after('card_hbl_total');
            $table->decimal('cash_total', 12, 2)->default(0)->after('cash_entries');

            // Split payments (part cash + part card/bank)
            $table->json('split_entries')->nullable()->after('cash_total');
            $table->decimal('split_total', 12, 2)->default(0)->after('split_entries');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn(['cash_entries', 'cash_total', 'split_entries', 'split_total']);
        });
    }
};
