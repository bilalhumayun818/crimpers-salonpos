<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            foreach (['split_cash', 'split_card', 'extra_amount', 'extra_cash_amount', 'extra_card_amount', 'extra_bank_amount'] as $column) {
                if (!Schema::hasColumn('invoices', $column)) {
                    $field = $table->decimal($column, 60, 2);
                    if (str_starts_with($column, 'split_')) {
                        $field->nullable();
                    } else {
                        $field->default(0);
                    }
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            foreach (['split_cash', 'split_card', 'extra_amount', 'extra_cash_amount', 'extra_card_amount', 'extra_bank_amount'] as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
