<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('invoices', 'pending_amount')) {
                $table->decimal('pending_amount', 60, 2)->default(0)->after('cash_received');
            }
            if (!Schema::hasColumn('invoices', 'split_bank')) {
                $table->decimal('split_bank', 60, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'pending_amount', 'split_bank']);
        });
    }
};
