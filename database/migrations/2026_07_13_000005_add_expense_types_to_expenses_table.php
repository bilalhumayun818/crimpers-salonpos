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
        Schema::table('expenses', function (Blueprint $table) {
            $table->enum('expense_type', ['daily', 'fixed', 'staff'])->default('daily')->after('branch_id');
            $table->string('category')->nullable()->after('expense_type');
            $table->foreignId('staff_id')->nullable()->constrained('staff')->cascadeOnDelete()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeignIdFor('staff');
            $table->dropColumn(['expense_type', 'category', 'staff_id']);
        });
    }
};
